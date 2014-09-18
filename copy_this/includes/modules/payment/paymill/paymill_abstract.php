<?php

require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/PaymentProcessor.php');
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/LoggingInterface.php');
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/Payments.php');
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/Clients.php');
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/FastCheckout.php');
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/WebHooks.php');

/**
 * Paymill payment plugin
 */
class paymill_abstract implements Services_Paymill_LoggingInterface
{

    var $code, $title, $description = '', $enabled, $privateKey, $logging, $fastCheckoutFlag;
    var $bridgeUrl = 'https://bridge.paymill.com/';
    var $apiUrl = 'https://api.paymill.com/v2/';
    var $version = '1.8.0';
    var $api_version = '2';

    /**
     * @var FastCheckout
     */
    var $fastCheckout;
    
    /**
     * @var Services_Paymill_Payments
     */
    var $payments;
    
    /**
     *
     * @var Services_Paymill_PaymentProcessor
     */
    var $paymentProcessor;

    function paymill_abstract()
    {
        $this->description = "<p style='font-weight: bold; text-align: center'>$this->version</p>";
        $this->paymentProcessor = new Services_Paymill_PaymentProcessor();


    }
    
    /**
     * @return FastCheckout
     */
    function getFastCheckout()
    {
        return $this->fastCheckout;
    }
    
    function update_status()
    {
        global $order;

        if (get_class($this) == 'paymill_cc') {
            $zone_id = MODULE_PAYMENT_PAYMILL_CC_ZONE;
        } elseif (get_class($this) == 'paymill_elv') {
            $zone_id = MODULE_PAYMENT_PAYMILL_ELV_ZONE;
        } else {
            $zone_id = 0;
        }

        if (($this->enabled == true) && ((int) $zone_id > 0)) {
            $check_flag = false;

            $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int) $zone_id . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while ($check = tep_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
        
        
        if (empty($this->privateKey) || empty($this->publicKey)) {
            $this->enabled = false;
        }
    }

    function pre_confirmation_check()
    {
        global $oscTemplate;

        $oscTemplate->addBlock('<link rel="stylesheet" type="text/css" href="ext/modules/payment/paymill/public/css/paymill.css" />', 'header_tags');
        $oscTemplate->addBlock('<script type="text/javascript">var PAYMILL_PUBLIC_KEY = "' . $this->publicKey . '";</script>', 'header_tags');
        $oscTemplate->addBlock('<script type="text/javascript" src="' . $this->bridgeUrl . '"></script>', 'header_tags');
    }

    function get_error()
    {
        global $_GET;
        $error = '';

        if (isset($_GET['error'])) {
            $error = urldecode($_GET['error']);
        }

        $error_text['error'] = html_entity_decode(constant('PAYMILL_'.strtoupper($error)));

        return $error_text;
    }

    function javascript_validation()
    {
        return false;
    }

    function selection()
    {
        return array('id' => $this->code,
            'module' => $this->public_title);
    }

    function confirmation()
    {
        return array('fields' => array());
    }

    function process_button()
    {
        return false;
    }

    function before_process()
    {
        global $order;

        $_SESSION['paymill_identifier'] = time();
        $this->paymentProcessor->setToken((string) $_POST['paymill_token']);
        $this->paymentProcessor->setAmount((int) $this->format_raw($order->info['total']));
        $this->paymentProcessor->setApiUrl((string) $this->apiUrl);
        $this->paymentProcessor->setCurrency((string) strtoupper($order->info['currency']));
        $this->paymentProcessor->setDescription(utf8_encode(substr((string) STORE_NAME, 0, 128)));
        $this->paymentProcessor->setEmail((string) $order->customer['email_address']);
        $this->paymentProcessor->setName((string) $order->customer['lastname'] . ', ' . $order->customer['firstname']);
        $this->paymentProcessor->setPrivateKey((string) $this->privateKey);
        $this->paymentProcessor->setLogger($this);
        $this->paymentProcessor->setSource($this->version . '_OSCOM_' . tep_get_version());
        $this->fastCheckout->setFastCheckoutFlag($this->fastCheckoutFlag);

        if ($_POST['paymill_token'] == 'dummyToken') {
            $this->fastCheckout();
        }

        $data = $this->fastCheckout->loadFastCheckoutData($_SESSION['customer_id']);
        
        if (array_key_exists('clientID', $data) && $data['clientID'] != '' && $data['clientID'] != null){
            $this->existingClient($data);
        }

        $result = $this->paymentProcessor->processPayment();
        $_SESSION['paymill']['transaction_id'] = $this->paymentProcessor->getTransactionId();


        if (!$result) {
            unset($_SESSION['paymill_identifier']);
            $errorCode = $this->paymentProcessor->getErrorCode();
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false) . '?step=step2&payment_error=' . $this->code . '&error='.$errorCode);
        }
        
        if ($this->fastCheckoutFlag) {
            $this->savePayment();
        } else {
            $this->saveClient();
        }
        
        unset($_SESSION['paymill_identifier']);
    }
    
    function existingClient($data)
    {
        global $order;
        if($this->fastCheckout->hasClient($_SESSION['customer_id'])){
            $client = $this->clients->getOne($data['clientID']);
            if ($client['email'] !== $order->customer['email_address']) {
                $this->clients->update(
                              array(
                                   'id' => $data['clientID'],
                                   'email' => $order->customer['email_address']
                              )
                );
            }


            $this->paymentProcessor->setClientId($client['id']);
        }
    }

    function fastCheckout()
    {
        $userId = $_SESSION['customer_id'];
        $data = $this->fastCheckout->loadFastCheckoutData($userId);

        if ($this->fastCheckout->canCustomerFastCheckoutCc($userId) && $this->code === 'paymill_cc') {
            if (!empty($data['paymentID_CC'])) {
                $this->paymentProcessor->setPaymentId($data['paymentID_CC']);
            }
        }

        if ($this->fastCheckout->canCustomerFastCheckoutElv($userId) && $this->code === 'paymill_elv') {
            if (!empty($data['paymentID_ELV'])) {
                $this->paymentProcessor->setPaymentId($data['paymentID_ELV']);
            }
        }
    }

    function savePayment()
    {
        if ($this->code === 'paymill_cc') {
            $this->fastCheckout->saveCcIds(
                $_SESSION['customer_id'], $this->paymentProcessor->getClientId(), $this->paymentProcessor->getPaymentId()
            );
        }

        if ($this->code === 'paymill_elv') {
            $this->fastCheckout->saveElvIds(
                $_SESSION['customer_id'], $this->paymentProcessor->getClientId(), $this->paymentProcessor->getPaymentId()
            );
        }
    }
    
    function saveClient()
    {
        if ($this->code === 'paymill_cc') {
            $this->fastCheckout->saveCcIds(
                $_SESSION['customer_id'], $this->paymentProcessor->getClientId(), ''
            );
        }

        if ($this->code === 'paymill_elv') {
            $this->fastCheckout->saveElvIds(
                $_SESSION['customer_id'], $this->paymentProcessor->getClientId(), ''
            );
        }
    }
    
    function after_process()
    {
        global $order, $insert_id;

        if (get_class($this) == 'paymill_cc') {
            $order_status_id = MODULE_PAYMENT_PAYMILL_CC_TRANSACTION_ORDER_STATUS_ID;
        } elseif (get_class($this) == 'paymill_elv') {
            $order_status_id = MODULE_PAYMENT_PAYMILL_ELV_TRANSACTION_ORDER_STATUS_ID;
        } else {
            $order_status_id = $order->info['order_status'];
        }

        $sql_data_array = array('orders_id' => $insert_id,
            'orders_status_id' => $order_status_id,
            'date_added' => 'now()',
            'customer_notified' => '0',
            'comments' => 'Payment approved, Transaction ID: ' . $_SESSION['paymill']['transaction_id']);

        tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

        $this->updateTransaction($_SESSION['paymill']['transaction_id'], $insert_id);
        tep_db_query("INSERT INTO pi_paymill_transaction (order_id, transaction_id, amount, payment_code) VALUES ('" . tep_db_prepare_input($insert_id) . "', '" . tep_db_prepare_input($_SESSION['paymill']['transaction_id']) . "', '" . (int) $this->format_raw($order->info['total']) . "', '" . tep_db_prepare_input($this->code) . "')");

        unset($_SESSION['paymill']);
    }

    function remove()
    {
        tep_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    function getOrderStatusTransactionID()
    {
        $check_query = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'Paymill [Transactions]' limit 1");

        if (tep_db_num_rows($check_query) < 1) {
            $status_query = tep_db_query("select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS);
            $status = tep_db_fetch_array($status_query);

            $status_id = $status['status_id'] + 1;

            $languages = tep_get_languages();

            foreach ($languages as $lang) {
                tep_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $status_id . "', '" . $lang['id'] . "', 'Paymill [Transactions]')");
            }

            $flags_query = tep_db_query("describe " . TABLE_ORDERS_STATUS . " public_flag");
            if (tep_db_num_rows($flags_query) == 1) {
                tep_db_query("update " . TABLE_ORDERS_STATUS . " set public_flag = 0 and downloads_flag = 0 where orders_status_id = '" . $status_id . "'");
            }
        } else {
            $check = tep_db_fetch_array($check_query);

            $status_id = $check['orders_status_id'];
        }

        return $status_id;
    }

    function log($messageInfo, $debugInfo)
    {
        if ($this->logging) {
            if (array_key_exists('paymill_identifier', $_SESSION)) {
                 tep_db_query("INSERT INTO `pi_paymill_logging` "
                            . "(debug, message, identifier) "
                            . "VALUES('" 
                              . tep_db_input($debugInfo) . "', '" 
                              . tep_db_input($messageInfo) . "', '" 
                              . tep_db_input($_SESSION['paymill_identifier']) 
                            . "')"
                );
            }
        }
    }

    function format_raw($number, $currency_code = '', $currency_value = '')
    {
        global $currencies, $currency;

        if (empty($currency_code) || !$currencies->is_set($currency_code)) {
            $currency_code = $currency;
        }

        if (empty($currency_value) || !is_numeric($currency_value)) {
            $currency_value = $currencies->currencies[$currency_code]['value'];
        }

        return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '', '');
    }

    function install()
    {
        tep_db_query("DROP TABLE IF EXISTS `pi_paymill_logging`");
        
        tep_db_query(
            "CREATE TABLE IF NOT EXISTS `pi_paymill_logging` ("
          . "`id` int(11) NOT NULL AUTO_INCREMENT,"
          . "`identifier` text NOT NULL,"
          . "`debug` text NOT NULL,"
          . "`message` text NOT NULL,"
          . "`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,"
          . "PRIMARY KEY (`id`)"
        . ") AUTO_INCREMENT=1"
        );
        
        tep_db_query(
            "CREATE TABLE IF NOT EXISTS `pi_paymill_fastcheckout` ("
           . "`userID` varchar(100),"
           . "`clientID` varchar(100),"
           . "`paymentID_CC` varchar(100),"
           . "`paymentID_ELV` varchar(100),"
           . "PRIMARY KEY (`userID`)"
         . ")"
        );

        tep_db_query(
            "CREATE TABLE IF NOT EXISTS `pi_paymill_webhooks` ("
            . "`id` varchar(100),"
            . "`url` varchar(150),"
            . "`mode` varchar(100),"
            . "`type` varchar(100),"
            . "`created_at` varchar(100),"
            . "PRIMARY KEY (`id`)"
            . ")"
        );

        tep_db_query(
            "CREATE TABLE IF NOT EXISTS `pi_paymill_transaction` ("
            . "`order_id` varchar(100),"
            . "`transaction_id` varchar(100),"
            . "`amount` varchar(100),"
            . "`payment_code` varchar(100),"
            . "PRIMARY KEY (`order_id`)"
            . ")"
        );

        $this->addOrderState('Paymill [Refund]');
        $this->addOrderState('Paymill [Chargeback]');
    }

    /**
     * Displays the register/remove Webhook button in the payment config.
     * @param String $type Can be either CC or ELV
     */
    function displayWebhookButton($type)
    {
        $privateKey = trim(constant("MODULE_PAYMENT_PAYMILL_".$type."_PRIVATEKEY"));
        if(empty($privateKey)){
            return;
        }
        $webhooks = new WebHooks($privateKey);
        $hooks = $webhooks->loadAllWebHooks($type);
        $action = empty($hooks) ? 'register' : 'remove';
        $buttonAction = 'CREATE';
        if($action === 'remove'){
            $buttonAction = 'REMOVE';
        }
        $buttonText = constant('MODULE_PAYMENT_PAYMILL_'.$type.'_WEBHOOKS_LINK_'.$buttonAction);

        $this->description .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>';
        $this->description .= '<script type="text/javascript" src="javascript/paymill_button_webhook.js"></script>';
        $this->description .= '<p><form id="register_webhooks" method="POST">';
        $parameters         = 'action='.$action.'&type='.$type;
        $this->description .= '<input id="listener" type="hidden" value="'.tep_href_link('../admin/paymill_webhook_listener.php',$parameters, 'SSL', false, false).'"> ';
        $this->description .= '<button type="submit">'.$buttonText.'</button></form></p>';
    }

    /**
     * Updates the description of target transaction by adding the prefix 'OrderID: ' followed by the order id
     * @param String $id
     * @param String $orderId
     */
    function updateTransaction($id, $orderId)
    {
        $transactions = new Services_Paymill_Transactions($this->privateKey, $this->apiUrl);
        $transaction = $transactions->getOne($id);
        $description = substr('OrderID: ' . $orderId . ' ' . $transaction['description'], 0, 128);
        $transactions->update(array(
                                   'id'          => $id,
                                   'description' => $description
                              ));

    }

    /**
     * Adds a new order state with the given name for both german and english language sets
     * Therefore the state name should be english
     * @param String $stateName
     */
    function addOrderState($stateName)
    {
        $check_query = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = '$stateName' limit 1");

        if (tep_db_num_rows($check_query) < 1) {
            $status_query = tep_db_query("select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS);
            $status = tep_db_fetch_array($status_query);
            $status_id = $status['status_id'] + 1;
        } else {
            $check = tep_db_fetch_array($check_query);
            $status_id = $check['orders_status_id'];
        }


        tep_db_query("REPLACE INTO orders_status (orders_status_id, language_id, orders_status_name) VALUES($status_id, 1, '".$stateName."')");
        tep_db_query("REPLACE INTO orders_status (orders_status_id, language_id, orders_status_name) VALUES($status_id, 2, '".$stateName."')");
    }

}

?>

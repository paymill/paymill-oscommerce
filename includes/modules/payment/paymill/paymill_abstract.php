<?php
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/PaymentProcessor.php');
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/LoggingInterface.php');

/**
 * Paymill payment plugin
 */
class paymill_abstract implements Services_Paymill_LoggingInterface
{

    var $code, $title, $description = '', $enabled, $privateKey, $logging;
    var $bridgeUrl = 'https://bridge.paymill.com/';
    var $apiUrl    = 'https://api.paymill.com/v2/';
    
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

        switch ($error) {
            case '100':
                $error_text['error'] = utf8_decode(MODULE_PAYMENT_PAYMILL_TEXT_ERROR_100);
                break;
            case '200':
                $error_text['error'] = utf8_decode(MODULE_PAYMENT_PAYMILL_TEXT_ERROR_200);
                break;
        }

        return $error_text;
    }
    
    function javascript_validation()
    {
        return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->public_title);
    }

    function confirmation()
    {
        return false;
    }
    
    function process_button()
    {
        return false;
    }
    
    function before_process()
    {
        global $order;

        $paymill = new Services_Paymill_PaymentProcessor();
        $paymill->setAmount((int) $this->format_raw($order->info['total']));
        $paymill->setApiUrl((string) $this->apiUrl);
        $paymill->setCurrency((string) strtoupper($order->info['currency']));
        $paymill->setDescription((string) STORE_NAME);
        $paymill->setEmail((string) $order->customer['email_address']);
        $paymill->setName((string) $order->customer['lastname'] . ', ' . $order->customer['firstname']);
        $paymill->setPrivateKey((string) $this->privateKey);
        $paymill->setToken((string) $_POST['paymill_token']);
        $paymill->setLogger($this);
        $paymill->setSource($this->version . '_OSCOM_' . tep_get_version());

        $result = $paymill->processPayment();
        $_SESSION['paymill']['transaction_id'] = $paymill->getTransactionId();

        if (!$result) {
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', true, false) . '?step=step2&payment_error=' . $this->code . '&error=200');
        }
    }

    function after_process()
    {
        global $order, $insert_id;

        $sql = "INSERT INTO  `orders_status_history` ("
                . "`orders_status_history_id` ,"
                . "`orders_id` ,"
                . "`orders_status_id` ,"
                . "`date_added` ,"
                . "`customer_notified` ,"
                . "`comments`"
             . ") VALUES("
                . "NULL, "
                . "'" . $insert_id . "', "
                . "'" . $order->info['order_status'] . "', "
                . "NOW(), "
                . "'0', "
                . "'Payment approved, Transaction ID: " . $_SESSION['paymill']['transaction_id'] . "'"
              . ")";


        tep_db_query($sql);
        
        unset($_SESSION['paymill']);
    }

    function remove()
    {
        tep_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    function log($messageInfo, $debugInfo)
    {
        if ($this->logging) {
            $logfile = dirname(__FILE__) . '/log.txt';
            if (file_exists($logfile) && is_writable($logfile)) {
                $handle = fopen($logfile, 'a');
                fwrite($handle, "[" . date(DATE_RFC822) . "] " . $messageInfo . "\n");
                fwrite($handle, "[" . date(DATE_RFC822) . "] " . $debugInfo . "\n");
                fclose($handle);
            }
        }
    }

    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies, $currency;

      if (empty($currency_code) || !$currencies->is_set($currency_code)) {
        $currency_code = $currency;
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '', '');
    }
}
?>

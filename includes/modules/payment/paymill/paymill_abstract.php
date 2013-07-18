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
    var $differentAmount = 0;
    
    function pre_confirmation_check()
    {
        global $order;

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }

        $_SESSION['paymill_token'] = $_POST['paymill_token'];
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
        $this->payment_action();
    }

    function payment_action()
    {
        global $order;

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }
        
        $paymill = new Services_Paymill_PaymentProcessor();
        $paymill->setAmount((int) (string) round($total * 100));
        $paymill->setApiUrl((string) $this->apiUrl);
        $paymill->setCurrency((string) strtoupper($order->info['currency']));
        $paymill->setDescription((string) STORE_NAME);
        $paymill->setEmail((string) $order->customer['email_address']);
        $paymill->setName((string) $order->customer['lastname'] . ', ' . $order->customer['firstname']);
        $paymill->setPrivateKey((string) $this->privateKey);
        $paymill->setToken((string) $_SESSION['paymill_token']);
        $paymill->setLogger($this);
        $paymill->setSource($this->version . '_' . str_replace(' ', '_', PROJECT_VERSION));
        
        if (array_key_exists('paymill_authorized_amount', $_SESSION)) {
            $paymill->setPreAuthAmount((int) (string) $_SESSION['paymill_authorized_amount']);
        }
        
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

    /**
     * Add the shipping tax to the order object
     *
     * @param order $order
     * @return float
     */
    public function getShippingTaxAmount(order $order)
    {
        return round($order->info['shipping_cost'] * ($this->getShippingTaxRate() / 100), 2);
    }

    public function getShippingTaxRate()
    {
        global $shipping;
        $shippingClasses = explode("_", $shipping['id']);
        $shippingClass = strtoupper($shippingClasses[0]);
        if (empty($shippingClass)) {
            $shippingTaxRate = 0;
        } else {
            $const = 'MODULE_SHIPPING_' . $shippingClass . '_TAX_CLASS';
            if (defined($const)) {
                $shippingTaxRate = tep_get_tax_rate(constant($const));
            } else {
                $shippingTaxRate = 0;
            }
        }

        return $shippingTaxRate;
    }
    
    public function log($messageInfo, $debugInfo)
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

    protected function getDifferentAmount()
    {
        $differenceAmount = $this->differentAmount;
        if(empty($differenceAmount) || !is_numeric($differenceAmount)) {
            $differenceAmount = 0;
        }
        
        return $differenceAmount;
    }
}
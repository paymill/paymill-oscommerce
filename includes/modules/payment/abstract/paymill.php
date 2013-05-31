<?php

require_once(dirname(dirname(__FILE__)) . '/lib/payintelligent/processPayment.php');

/**
 * Paymill payment plugin
 */
class paymill
{

    var $code, $title, $description = '', $enabled, $privateKey, $apiUrl;

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
        return false;
    }

    function get_error()
    {
        global $_GET, $language;
        $error = '';
        
        if (isset($_GET['error'])) {
            $error = urldecode($_GET['error']);
        }

        @include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/modules/payment/' . $this->code . '.php');
        
        switch ($error) {
            case '100':
                $error_text['error'] = MODULE_PAYMENT_PAYMILL_TEXT_ERROR_100;
                break;
            case '200':
                $error_text['error'] = MODULE_PAYMENT_PAYMILL_TEXT_ERROR_200;
                break;
        }

        return $error_text;
    }

    function update_status()
    {
        return false;
    }

    function javascript_validation()
    {
        return '';
    }

    function after_process()
    {
        global $order, $insert_id;

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }
        
        $paymill = new processPayment($this);
        $authorizedAmount = $_SESSION['pi']['paymill_amount'];
        
        if ($this->code === 'paymill_elv') {
            $authorizedAmount = $total;
        }
        
        $result = $paymill->processPayment(array(
            'token' => $_SESSION['paymill_token'],
            'authorizedAmount' => $authorizedAmount * 100,
            'amount' => $total * 100,
            'currency' => strtoupper($order->info['currency']),
            'name' => $order->customer['lastname'] . ', ' . $order->customer['firstname'],
            'email' => $order->customer['email_address'],
            'description' => STORE_NAME . ' Bestellnummer: ' . $insert_id,
        ));
        
        if (!$result) {
            tep_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status = (SELECT orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name LIKE '%Paymill%' GROUP by orders_status_id) WHERE orders_id = '" . $insert_id . "'");
            $url = tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code, 'SSL', true, false);
            tep_redirect($url . '&error=200');
        }

        unset($_SESSION['pi']);

        return true;
    }
}

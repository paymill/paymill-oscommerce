<?php
require_once('includes/application_top.php');
require_once(DIR_WS_CLASSES . 'order.php');
require_once(dirname(__FILE__) . '/../ext/modules/payment/paymill/lib/Services/Paymill/PaymentProcessor.php');
if (isset($_GET['oID']) && !empty($_GET['oID'])) {
    $order = new order($_GET['oID']);
    $transaction = tep_db_fetch_array(tep_db_query("SELECT * FROM pi_paymill_transaction WHERE order_id = '" . $_GET['oID'] . "'"));
    require_once(dirname(__FILE__) . '/../includes/modules/payment/' . $transaction['payment_code'] . '.php');
    include(dirname(__FILE__) . '/../includes/languages/' . $_SESSION['language'] . '/modules/payment/' . $transaction['payment_code'] . '.php');

    $payment = new $transaction['payment_code']();
    
    $params = array();
    $params['amount'] = $transaction['amount'];
    $params['currency'] = $order->info['currency'];

    $paymentProcessor = new Services_Paymill_PaymentProcessor(
        $payment->privateKey, 
        $payment->apiUrl,
        null, 
        $params, 
        $payment
    );

    $paymentProcessor->setPreauthId($transaction['preauth_id']);

    try {
        $result = $paymentProcessor->capture();
    } catch (Exception $ex) {
    }

    if ($result) {
        $statusArray = tep_db_fetch_array(tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'Paymill [Captured]' limit 1"));
        tep_db_query("UPDATE " . TABLE_ORDERS . " SET orders_status='" . $statusArray['orders_status_id'] . "' WHERE orders_id='" . $_GET['oID'] . "'");

        tep_db_query("UPDATE pi_paymill_transaction SET transaction_id = '" . tep_db_prepare_input($paymentProcessor->getTransactionId()) . "' WHERE order_id = " . (int) $_GET['oID']);
        
        $messageStack->add_session(PAYMILL_CAPTURE_SUCCESS, 'success');
    } else {
        $messageStack->add_session(PAYMILL_CAPTURE_ERROR, 'error');
    }
}

tep_redirect(tep_href_link(FILENAME_ORDERS, 'oID=' . $_GET['oID'] . '&action=edit', true, false));
<?php
require_once('abstract/FastCheckoutAbstract.php');
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/Clients.php');
require_once(DIR_FS_CATALOG . 'ext/modules/payment/paymill/lib/Services/Paymill/Payments.php');
class FastCheckout extends FastCheckoutAbstract
{
    /**
     * Executes sql query
     *
     * @param $sql
     *
     * @return resource
     */
    function dbQuery($sql)
    {
        return tep_db_query($sql);
    }

    /**
     * Executes sql statements returning an array
     * @param $sql
     *
     * @return array|bool|mixed
     */
    function dbFetchArray($sql)
    {
        $data = tep_db_fetch_array(tep_db_query($sql));
        
        if (!is_array($data)) {
            $data = array();
        }
        
        return $data;
    }

    /**
     * Returns the name of the Fast Checkout Table as a string
     * @return string
     */
    function getFastCheckoutTableName()
    {
        return "pi_paymill_fastcheckout";
    }
}
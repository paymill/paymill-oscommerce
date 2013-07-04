<?php

require_once('paymill/abstract/paymill.php');

class paymill_elv extends paymill
{

    function paymill_elv()
    {
        $this->code = 'paymill_elv';
        $this->version = '1.0.5';
        $this->title = MODULE_PAYMENT_PAYMILL_ELV_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_PAYMILL_ELV_TEXT_PUBLIC_TITLE;
        $this->sort_order = MODULE_PAYMENT_PAYMILL_ELV_SORT_ORDER;
        $this->enabled = ((MODULE_PAYMENT_PAYMILL_ELV_STATUS == 'True') ? true : false);
        $this->privateKey = trim(MODULE_PAYMENT_PAYMILL_ELV_PRIVATEKEY);
        $this->order_status = MODULE_PAYMENT_PAYMILL_ELV_ORDER_STATUS_ID;
        $this->logging = MODULE_PAYMENT_PAYMILL_ELV_LOGGING;
        $this->publicKey = MODULE_PAYMENT_PAYMILL_ELV_PUBLICKEY;
    }

    function selection()
    {
        $resourcesDir = HTTP_SERVER . DIR_WS_CATALOG . '/includes/modules/payment/paymill/resources/';

        $formArray = array();

        $formArray[] = array(
            'title' => '',
            'field' => '<link rel="stylesheet" type="text/css" href="' . HTTP_SERVER . DIR_WS_CATALOG . 'css/paymill.css"/>'
        );

        $formArray[] = array(
            'title' => '',
            'field' => tep_image($resourcesDir . 'icon_elv.png')
        );

        $formArray[] = array(
            'title' => MODULE_PAYMENT_PAYMILL_ELV_TEXT_ACCOUNT,
            'field' => '<br/><input type="text" id="account-number" class="form-row-paymill"/>'
        );

        $formArray[] = array(
            'title' => MODULE_PAYMENT_PAYMILL_ELV_TEXT_BANKCODE,
            'field' => '<br/><input type="text" id="bank-code" class="form-row-paymill"/>'
        );

        $formArray[] = array(
            'title' => MODULE_PAYMENT_PAYMILL_ELV_TEXT_ACCOUNT_HOLDER,
            'field' => '<br/><input type="text" id="bank-owner" class="form-row-paymill"/>'
        );

        $formArray[] = array(
            'field' =>
            '<div class="form-row">'
            . '<div class="paymill_powered">'
            . '<div class="paymill_credits">'
            . MODULE_PAYMENT_PAYMILL_ELV_TEXT_SAVED
            . ' <a href="http://www.paymill.de" target="_blank">Paymill</a>'
            . '</div>'
            . '</div>'
            . '</div>'
        );

        $script = '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>'
                . '<script type="text/javascript">'
                    . 'var PAYMILL_PUBLIC_KEY = "' . $this->publicKey . '";'
                . '</script>'
                . '<script type="text/javascript" src="' . $this->bridgeUrl . '"></script>'
                . '<script type="text/javascript">'
                . 'var elvlogging = "' . MODULE_PAYMENT_PAYMILL_ELV_LOGGING . '";'
                . 'var elv_account_number_invalid = "' . utf8_decode(MODULE_PAYMENT_PAYMILL_ELV_TEXT_ACCOUNT_INVALID) . '";'
                . 'var elv_bank_code_invalid = "' . utf8_decode(MODULE_PAYMENT_PAYMILL_ELV_TEXT_BANKCODE_INVALID) . '";'
                . 'var elv_bank_owner_invalid = "' . utf8_decode(MODULE_PAYMENT_PAYMILL_ELV_TEXT_ACCOUNT_HOLDER_INVALID) . '";'
                . file_get_contents(DIR_FS_CATALOG . 'javascript/paymill_elv_checkout.js')
                . '</script>';

        $formArray[] = array(
            'title' => "",
            'field' => $script
        );

        $selection = array(
            'id' => $this->code,
            'module' => $this->title,
            'fields' => $formArray,
            'description' => $this->info
        );

        return $selection;
    }

    function check()
    {
        if (!isset($this->_check)) {
            $check_query = tep_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_PAYMILL_ELV_STATUS'");
            $this->_check = tep_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function install()
    {
        global $language;
        @include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/modules/payment/paymill_elv.php');

        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_ELV_STATUS_TITLE . "', 'MODULE_PAYMENT_PAYMILL_ELV_STATUS', 'True', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_ELV_ALLOWED_TITLE . "', 'MODULE_PAYMENT_PAYMILL_ELV_ALLOWED', '', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_ELV_SORT_ORDER_TITLE . "', 'MODULE_PAYMENT_PAYMILL_ELV_SORT_ORDER', '0', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_ELV_PRIVATEKEY_TITLE . "', 'MODULE_PAYMENT_PAYMILL_ELV_PRIVATEKEY', '0', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_ELV_PUBLICKEY_TITLE . "', 'MODULE_PAYMENT_PAYMILL_ELV_PUBLICKEY', '0', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_ELV_ADD_AMOUNT_TITLE . "', 'MODULE_PAYMENT_PAYMILL_ELV_ADD_AMOUNT', '10', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_PAYMILL_ELV_ORDER_STATUS_ID_TITLE . "', 'MODULE_PAYMENT_PAYMILL_ELV_ORDER_STATUS_ID', '0',  '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_ELV_LOGGING_TITLE . "', 'MODULE_PAYMENT_PAYMILL_ELV_LOGGING', 'False', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }

    function remove()
    {
        tep_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
        return array(
            'MODULE_PAYMENT_PAYMILL_ELV_STATUS',
            'MODULE_PAYMENT_PAYMILL_ELV_LOGGING',
            'MODULE_PAYMENT_PAYMILL_ELV_PRIVATEKEY',
            'MODULE_PAYMENT_PAYMILL_ELV_PUBLICKEY',
            'MODULE_PAYMENT_PAYMILL_ELV_ORDER_STATUS_ID',
            'MODULE_PAYMENT_PAYMILL_ELV_SORT_ORDER',
            'MODULE_PAYMENT_PAYMILL_ELV_ALLOWED'
        );
    }

}

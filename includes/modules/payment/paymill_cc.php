<?php

require_once('paymill/paymill_abstract.php');

class paymill_cc extends paymill_abstract
{
    function paymill_cc()
    {
    	global $oscTemplate;

        $this->code = 'paymill_cc';
        $this->version = '1.0.7';
        $this->api_version = '2';
        $this->title = MODULE_PAYMENT_PAYMILL_CC_TEXT_TITLE;
        $this->public_title = MODULE_PAYMENT_PAYMILL_CC_TEXT_PUBLIC_TITLE;

        if ( defined('MODULE_PAYMENT_PAYMILL_CC_STATUS') ) {
        	$this->enabled = ((MODULE_PAYMENT_PAYMILL_CC_STATUS == 'True') ? true : false);
        	$this->sort_order = MODULE_PAYMENT_PAYMILL_CC_SORT_ORDER;
        	$this->privateKey = trim(MODULE_PAYMENT_PAYMILL_CC_PRIVATEKEY);
        	$this->logging = ((MODULE_PAYMENT_PAYMILL_CC_LOGGING == 'True') ? true : false);
        	$this->publicKey = MODULE_PAYMENT_PAYMILL_CC_PUBLICKEY;
        	$this->differentAmount = MODULE_PAYMENT_PAYMILL_CC_ADD_AMOUNT;

        	if ((int)MODULE_PAYMENT_PAYMILL_CC_ORDER_STATUS_ID > 0) {
        		$this->order_status = MODULE_PAYMENT_PAYMILL_CC_ORDER_STATUS_ID;
        	}
        }

        if ( isset($oscTemplate) ) {
        	$oscTemplate->addBlock('<link rel="stylesheet" type="text/css" href="ext/modules/payment/paymill/public/css/paymill.css" />', 'header_tags');
        	$oscTemplate->addBlock('<script type="text/javascript">var PAYMILL_PUBLIC_KEY = "' . $this->publicKey . '";</script>', 'header_tags');
        	$oscTemplate->addBlock('<script type="text/javascript" src="' . $this->bridgeUrl . '"></script>', 'header_tags');
        }
    }

    function selection()
    {
        global $order;

        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $total = $order->info['total'] + $order->info['tax'];
        } else {
            $total = $order->info['total'];
        }

        $amount = $total + $this->getShippingTaxAmount($order);

        $_SESSION['paymill_authorized_amount'] = ($amount + $this->getDifferentAmount()) * 100;
        
        $today = getdate();
        for ($i = $today['year']; $i < $today['year'] + 10; $i++) {//
            $expires_year[] = array(
                'id' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
                'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
            );
        }
        
        $expires_month = array();
        $expires_month[] = array('id' => '01', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_JANUARY);
        $expires_month[] = array('id' => '02', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_FEBRUARY);
        $expires_month[] = array('id' => '03', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_MARCH);
        $expires_month[] = array('id' => '04', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_APRIL);
        $expires_month[] = array('id' => '05', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_MAY);
        $expires_month[] = array('id' => '06', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_JUNE);
        $expires_month[] = array('id' => '07', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_JULY);
        $expires_month[] = array('id' => '08', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_AUGUST);
        $expires_month[] = array('id' => '09', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_SEPTEMBER);
        $expires_month[] = array('id' => '10', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_OCTOBER);
        $expires_month[] = array('id' => '11', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_NOVEMBER);
        $expires_month[] = array('id' => '12', 'text' => MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_DECEMBER);

        $months_string = '';
        foreach ($expires_month as $m) {
            $months_string .= '<option value="' . $m['id'] . '">' . $m['text'] . '</option>';
        }

        $years_string = '';
        foreach ($expires_year as $y) {
            $years_string .= '<option value="' . $y['id'] . '">' . $y['text'] . '</option>';
        }

        $formArray = array();

        $this->accepted = tep_image('ext/modules/payment/paymill/public/images/icon_mastercard.png') . " " . tep_image('ext/modules/payment/paymill/public/images/icon_visa.png');

        $formArray[] = array(
        	'title' => null,
        	'field' => $this->accepted
        );

        $formArray[] = array(
            'title' => MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_NUMBER,
            'field' => '<input type="text" id="card-number" class="form-row-paymill" />'
        );

        $formArray[] = array(
            'title' => MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_EXPIRY,
            'field' => '<span class="paymill-expiry"><select id="card-expiry-month">' . $months_string . '</select>'
                     . '&nbsp;'
                     . '<select id="card-expiry-year">' . $years_string . '</select></span>'
        );

        $formArray[] = array(
            'title' => MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_CVC,
            'field' => '<span class="card-cvc-row"><input type="text" size="4" id="card-cvc" class="form-row-paymill" /></span>'
            		 . '&nbsp;'
            		 . '<a href="javascript:popupWindow(\'' . tep_href_link(FILENAME_POPUP_CVV, '', 'SSL') . '\')">Info</a>'
        );

        $script = '<script type="text/javascript">'
        		. 'var cclogging = "' . MODULE_PAYMENT_PAYMILL_CC_LOGGING . '";'
        		. 'var cc_expiery_invalid = "' . utf8_decode(MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_EXPIRY_INVALID) . '";'
        		. 'var cc_card_number_invalid = "' . utf8_decode(MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_CARDNUMBER_INVALID) . '";'
        		. 'var cc_cvc_number_invalid = "' . utf8_decode(MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_CVC_INVALID) . '";'
        		. file_get_contents(DIR_FS_CATALOG . 'ext/modules/payment/paymill/public/javascript/cc.js')
        		. '</script>';

        $formArray[] = array(
        	'title' => null,
        	'field' => '<div class="form-row">'
        			 . '  <div class="paymill_powered">'
        			 . '    <div class="paymill_credits">'
        			 . MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_SAVED
        			 . '      <a href="http://www.paymill.de" target="_blank">PAYMILL</a>'
        			 . '    </div>'
        			 . '  </div>'
        			 . '</div>'
        			 . '<input type="hidden" value="' . $_SESSION['paymill_authorized_amount'] . '" id="amount" name="amount" />'
        			 . '<input type="hidden" value="' . strtoupper($order->info['currency']) . '" id="currency" name="currency" />'
        			 . $script
        );

        $selection = array(
            'id' => $this->code,
            'module' => $this->public_title,
            'fields' => $formArray
        );

        return $selection;
    }

    function check()
    {
        if (!isset($this->_check)) {
            $check_query = tep_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_PAYMILL_CC_STATUS'");
            $this->_check = tep_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function install()
    {
        global $language;
        
        include(DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language . '/modules/payment/paymill_cc.php');
        
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_description, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_CC_STATUS_TITLE . "', '" . MODULE_PAYMENT_PAYMILL_CC_STATUS_DESC . "', 'MODULE_PAYMENT_PAYMILL_CC_STATUS', 'True', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_description, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_CC_ALLOWED_TITLE . "', '" . MODULE_PAYMENT_PAYMILL_CC_ALLOWED_DESC . "', 'MODULE_PAYMENT_PAYMILL_CC_ALLOWED', '', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_description, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_CC_SORT_ORDER_TITLE . "', '" . MODULE_PAYMENT_PAYMILL_CC_SORT_ORDER_DESC . "', 'MODULE_PAYMENT_PAYMILL_CC_SORT_ORDER', '0', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_description, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_CC_PRIVATEKEY_TITLE . "', '" . MODULE_PAYMENT_PAYMILL_CC_PRIVATEKEY_DESC . "', 'MODULE_PAYMENT_PAYMILL_CC_PRIVATEKEY', '0', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_description, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_CC_PUBLICKEY_TITLE . "', '" . MODULE_PAYMENT_PAYMILL_CC_PUBLICKEY_DESC . "', 'MODULE_PAYMENT_PAYMILL_CC_PUBLICKEY', '0', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_description, configuration_key, configuration_value, configuration_group_id, sort_order, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_CC_ADD_AMOUNT_TITLE . "', '" . MODULE_PAYMENT_PAYMILL_CC_ADD_AMOUNT_DESC . "', 'MODULE_PAYMENT_PAYMILL_CC_ADD_AMOUNT', '10', '6', '0', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_description, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added) values ('" . MODULE_PAYMENT_PAYMILL_CC_ORDER_STATUS_ID_TITLE . "', '" . MODULE_PAYMENT_PAYMILL_CC_ORDER_STATUS_ID_DESC . "', 'MODULE_PAYMENT_PAYMILL_CC_ORDER_STATUS_ID', '0',  '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
        tep_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_description, configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('" . MODULE_PAYMENT_PAYMILL_CC_LOGGING_TITLE . "', '" . MODULE_PAYMENT_PAYMILL_CC_LOGGING_DESC . "', 'MODULE_PAYMENT_PAYMILL_CC_LOGGING', 'False', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
    }

    function remove()
    {
        tep_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    }

    function keys()
    {
        return array(
            'MODULE_PAYMENT_PAYMILL_CC_STATUS',
            'MODULE_PAYMENT_PAYMILL_CC_LOGGING',
            'MODULE_PAYMENT_PAYMILL_CC_PRIVATEKEY',
            'MODULE_PAYMENT_PAYMILL_CC_PUBLICKEY',
            'MODULE_PAYMENT_PAYMILL_CC_ADD_AMOUNT',
            'MODULE_PAYMENT_PAYMILL_CC_ORDER_STATUS_ID',
            'MODULE_PAYMENT_PAYMILL_CC_SORT_ORDER',
            'MODULE_PAYMENT_PAYMILL_CC_ALLOWED'
        );
    }

}

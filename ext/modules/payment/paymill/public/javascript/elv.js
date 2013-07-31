var isElvSubmitted = false;
$(document).ready(function () {
    if (typeof $.fn.prop !== 'function') {
        $.fn.prop = function(name, value) {
            if (typeof value === 'undefined') {
                return this.attr(name);
            } else {
                return this.attr(name, value);
            }
        };
    }

    $('#account-name-field').html('<input type="text" value="' + paymill_elv_holder + '" id="bank-owner" class="form-row-paymill" />');
    $('#account-number-field').html('<input type="text" value="' + paymill_elv_account + '" id="account-number" class="form-row-paymill" />');
    $('#bank-code-field').html('<input type="text" value="' + paymill_elv_code + '" id="bank-code" class="form-row-paymill" />');

    $('form[name="checkout_confirmation"]').submit(function () {
		if (!isElvSubmitted) {
			if (!paymill_elv_fastcheckout) {
				if (false === paymill.validateAccountNumber($('#account-number').val())) {
					alert(elv_account_number_invalid);
					return false;
				}

				if (false === paymill.validateBankCode($('#bank-code').val())) {
					alert(elv_bank_code_invalid);
					return false;
				}

				if ($('#bank-owner').val() === "") {
					alert(elv_bank_owner_invalid);
					return false; 
				}

				paymill.createToken({
					number:        $('#account-number').val(),
					bank:          $('#bank-code').val(),
					accountholder: $('#bank-owner').val()
				}, PaymillElvResponseHandler);

				return false;
			} else {
				$('#paymill_form').html('<input type="hidden" name="paymill_token" value="dummyToken" />').submit();
			}
		}
    });
	
	$('#bank-owner').focus(function() {
        paymill_elv_fastcheckout = false;
        $('#bank-owner').val('');
    });
    
    $('#account-number').focus(function() {
		$('#account-number').val('');
        paymill_elv_fastcheckout = false;
    });
    
    $('#bank-code').focus(function() {
		$('#bank-code').val('');
        paymill_elv_fastcheckout = false;
    });
	

    function PaymillElvResponseHandler(error, result) 
    { 
		isElvSubmitted = true;
        if (error) {
			isElvSubmitted = false;
            console.log("An API error occured: " + error.apierror);
            return false;
        } else {
            console.log(result.token);
            $('#paymill_form').html('<input type="hidden" name="paymill_token" value="' + result.token + '" />').submit();
            return false;
        }
    }
});

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
    $('#bank-code-field').html('<input type="text" value="' + paymill_elv_bank + '" id="bank-code" class="form-row-paymill" />');

    $('form[name="checkout_confirmation"]').submit(function () {
        if (!isElvSubmitted) {
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
        }
    });

    function PaymillElvResponseHandler(error, result) 
    { 
        isElvSubmitted = true;
        if (error) {
            console.log("An API error occured: " + error.apierror);
            return false;
        } else {
            console.log(result.token);
            $('#paymill_form').html('<input type="hidden" name="paymill_token" value="' + result.token + '" />').submit();
            return false;
        }
    }
});

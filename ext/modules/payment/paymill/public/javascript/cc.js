var isCcSubmitted = false;
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

    $('form[name="checkout_confirmation"]').submit(function () {
        if (!isCcSubmitted) {
            if (!paymill.validateExpiry($("#card-expiry-month option:selected").val(), $("#card-expiry-year option:selected").val())) {
                alert(cc_expiery_invalid);
                return false;
            }

            if (!paymill.validateCardNumber($("#card-number").val())) {
                alert(cc_card_number_invalid);
                return false;
            }

            if (!paymill.validateCvc($("#card-cvc").val())) {
                alert(cc_cvc_number_invalid);
                return false;
            }

            paymill.createToken({
                number: $("#card-number").val(),
                exp_month: $("#card-expiry-month option:selected").val(), 
                exp_year: $("#card-expiry-year option:selected").val(), 
                cvc: $("#card-cvc").val(),
                amount_int: paymill_total,
                currency: paymill_currency,
                cardholder: $("#card-owner").val()
            }, PaymillCcResponseHandler);

            return false; 
        }
    });

    function PaymillCcResponseHandler(error, result) 
    { 
        isCcSubmitted = true;
        if (error) {
            isCcSubmitted = false;
            console.log("An API error occured: " + error.apierror);
            return false;
        } else {
            console.log(result.token);
            $('form[name="checkout_confirmation"]').attr('action', form_post_to);
            $('form[name="checkout_confirmation"]').append("<input type='hidden' name='paymill_token' value='" + result.token + "'/>");
            $('form[name="checkout_confirmation"]').submit();
            return false;
        }
    }

});

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

    $('#card-owner-field').html('<input type="text" value="' + paymill_card_owner + '" id="card-owner" class="form-row-paymill" />');
    $('#card-number-field').html('<input type="text" id="card-number" class="form-row-paymill" />');
    $('#card-expiry-month-field').html('<select id="card-expiry-month"></select>');
    $('#card-expiry-year-field').html('<select id="card-expiry-year"></select>');
    $('#card-cvc-field').html('<input type="text" id="card-cvc" class="form-row-paymill" size="5" maxlength="4" />');

    for ( var cc_month_counter in paymill_cc_months ) {
        var cc_month_value = paymill_cc_months[cc_month_counter][0];
        var cc_month_text = paymill_cc_months[cc_month_counter][1];

        $('<option/>').val(cc_month_value).text(cc_month_text).appendTo($('#card-expiry-month'));
    };

    for ( var cc_year_counter in paymill_cc_years ) {
        var cc_year_value = paymill_cc_years[cc_year_counter][0];
        var cc_year_text = paymill_cc_years[cc_year_counter][1];

        $('<option/>').val(cc_year_value).text(cc_year_text).appendTo($('#card-expiry-year'));
    };

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
            $('#paymill_form').html('<input type="hidden" name="paymill_token" value="' + result.token + '" />').submit();
            return false;
        }
    }

});

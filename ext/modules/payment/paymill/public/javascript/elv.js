var isElvSubmitted = false;
var oldFieldData;
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

    PaymillCreateElvForm();
	oldFieldData = getFormData();

    $('form[name="checkout_confirmation"]').submit(function (e) {
        e.preventDefault();
		var newFieldData = getFormData();
        if (!isElvSubmitted) {
            if (paymill_elv_holder !== "" && oldFieldData.toString() === newFieldData.toString()) {
				$('#paymill_form').html('<input type="hidden" name="paymill_token" value="dummyToken" />').submit();
                return false;
            } else {
                hideErrorBoxes();
                var elvErrorFlag = true;

                if ($('#paymill-bank-owner').val() === "") {
                    $("#elv-holder-error").text($("<div/>").html(elv_bank_owner_invalid).text());
                    $("#elv-holder-error").css('display', 'block');
                    elvErrorFlag = false;
                }

                if(isSepa()){
                    elvErrorFlag = PaymillValidateSepaForm(elvErrorFlag);
                } else {
                    elvErrorFlag = PaymillValidateOldElvForm(elvErrorFlag);
                }
				
				if (!elvErrorFlag) {
					return elvErrorFlag;
				}

                PaymillCreateElvToken();

                return false;
            }
        }
    });
});

function getFormData(ignoreEmptyValues) 
{
	var array = new Array();
	$('form[name="checkout_confirmation"] :input').not('[type=hidden]').each(function() 
	{

		if ($(this).val() === "" && ignoreEmptyValues) {
			return;
		}

		array.push($(this).val());
	});

	return array;
}

function PaymillValidateSepaForm(elvErrorFlag)
{
    console.log("Starting Validation for SEPA form...");

    var iban = new Iban();

    if(!iban.validate($('#paymill-iban').val())){
        $('#elv-iban-error').text($("<div/>").html(elv_iban_invalid).text());
        $('#elv-iban-error').css('display', 'block');
        elvErrorFlag = false;
    }

    if($('#paymill-bic').val() === ''){
        $('#elv-bic-error').text($("<div/>").html(elv_bic_invalid).text());
        $('#elv-bic-error').css('display', 'block');
        elvErrorFlag = false;
    }

    return elvErrorFlag;
}

function PaymillValidateOldElvForm(elvErrorFlag)
{
    console.log("Starting Validation for old form...");
    
    if (!paymill.validateBankCode($('#paymill-bic').val())) {
        $("#elv-bic-error").text($("<div/>").html(elv_bank_code_invalid).text());
        $("#elv-bic-error").css('display', 'block');
        elvErrorFlag = false;
    }
	
    if (!paymill.validateAccountNumber($('#paymill-iban').val())) {
        $("#elv-iban-error").text($("<div/>").html(elv_account_number_invalid).text());
        $("#elv-iban-error").css('display', 'block');
        elvErrorFlag = false;
    }

    return elvErrorFlag;
}

function PaymillCreateElvForm()
{
	if (paymill_elv_iban === "") {
		paymill_elv_iban = paymill_elv_account;
		paymill_elv_bic = paymill_elv_code;
	}
	
    $('#account-name-field').html('<input type="text" value="' + paymill_elv_holder + '" id="paymill-bank-owner" class="form-row-paymill" />');
	$('#iban-field').html('<input type="text" value="' + paymill_elv_iban + '" id="paymill-iban" class="form-row-paymill" />');
	$('#bic-field').html('<input type="text" value="' + paymill_elv_bic + '" id="paymill-bic" class="form-row-paymill" />');
}

function PaymillCreateElvToken()
{
    if(!isSepa()){
        paymill.createToken({
            number:        $('#paymill-iban').val(),
            bank:          $('#paymill-bic').val(),
            accountholder: $('#paymill-bank-owner').val()
        }, PaymillElvResponseHandler);
    } else {
        paymill.createToken({
            iban:          $('#paymill-iban').val(),
            bic:           $('#paymill-bic').val(),
            accountholder: $('#paymill-bank-owner').val()
        }, PaymillElvResponseHandler);
	}
}

function isSepa() 
{
	var reg = new RegExp(/^\D{2}/);
	return reg.test($('#paymill-iban').val());
}

function hideErrorBoxes()
{
    $("#elv-holder-error").css('display', 'none');
	$("#elv-iban-error").css('display', 'none');
	$("#elv-bic-error").css('display', 'none');
}

function PaymillElvResponseHandler(error, result)
{
    isElvSubmitted = true;
    if (error) {
        isElvSubmitted = false;
        console.log(error);
        window.location = $("<div/>").html(checkout_payment_link + error.apierror).text();
        return false;
    } else {
        $('#paymill_form').html('<input type="hidden" name="paymill_token" value="' + result.token + '" />').submit();
        return false;
    }
}
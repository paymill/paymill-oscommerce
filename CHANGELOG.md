#Changelog
##1.7.0
* add payment logos
* merge elv / sepa form
* add pre notification to email and invoice


##1.6.0
* Added Language Support for german, english, french, italian, spanish and portuguese
* Added improved early pan detection
* Added iban validation

##1.5.0
* Added SEPA Payment Form for german Direct Debit
* Added WebHooks. WebHooks will automatically synch your shops order states on refund or chargeback events
* Updated Fast Checkout
* Removed Paymill Label
* Added version number to payment configuration
* Fixed Log view
* Added improved feedback on errors for both bridge and api errors

##1.4.4
* update fast checkout

##1.4.3
* Fixed an issue with the error display

##1.4.2
* Improved Feedback in all errorcases.

##1.4.1
* Fixed a bug causing inital plugin installation to be impossible

##1.4.0
* Changed the german name of the direct debit method to 'ELV'
* Implemented credit card support for Maestro Cards
* Improved the way, credit card icons are beeing displayed on card identification
* Reworked log view

##1.2.1
* Added admin view for the log

##1.3.1
* Increased the log paging count
* Improved logging mechanics
* Removed legacy files and code no longer in use

##1.0.9
* Merged Pull Request from Harald Ponce de Leon <hpdl@oscommerce.com>

 * Move form payment input fields from the checkout payment page to the checkout confirmation page (values are not sent to the server for pci compliance)
 * Add Credit Card Owner field
 * Remove getDifferentAmount()
 * Remove localized month names (automatically taken care of by strftime)
 * Update setSource() value to use tep_get_version()
 * Removed getShippingTaxAmount() and getShippingTaxRate()
 * Move file_get_contents(javascript) to header_tags

##1.0.8
* Merged Pull Request from Harald Ponce de Leon <hpdl@oscommerce.com>
 * Move javascript to <head> as recommended by the documentation (this is done through v2.3's header tags feature)
 * Move public resources to ext/modules/payment/paymill/public/
 * Add Paymill to the module title
 * Show the module public title on the payment selection page
 * Use the correct order status id for orders
 * Move Paymill library to ext/modules/payment/paymill/lib
 * Move resource images to ext/modules/payment/paymill/public/images
 * Move log to includes/modules/payment/paymill
 * Move and rename abstract class to includes/modules/payment/paymill/paymill_abstract.php

##1.0.7
* Updated PAYMILL lib

##1.0.6
* Fixed a bug causing the debug data to be logged without refering to the loggin option
* Added different amount to the preAuth process to allow 3-D Secure
* Removed legacy files no longer used
* Fixed several minor bugs

##1.0.5
* Added missing error messages
* Fixed several minor bugs

##1.0.4
* Imported xtc module changes

##1.0.3 - Start of version control
* Added support for the english language
* Fixed several minor bugs

##1.1.1
* Redesigned PAYMILL lables
* Fast Checkout added
* Multiple minor bugfixes
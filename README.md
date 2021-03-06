PAYMILL - osCommerce 2.3
========================

osCommerce (Version 2.3) Plugin for PAYMILL credit card and elv payments

# Update Note

To update the osCommerce PAYMILL plugin you must reinstall the plugin to ensure 
that all needed tables are created.

## Your Advantages
* PCI DSS compatibility
* Payment means: Credit Card (Visa, Visa Electron, Mastercard, Maestro, Diners, Discover, JCB, AMEX), Direct Debit (ELV)
* Optional fast checkout configuration allowing your customers not to enter their payment detail over and over during checkout
* Improved payment form with visual feedback for your customers
* Supported Languages: German, English, Spanish, French, Italian, Portuguese
* Backend Log with custom View accessible from your shop backend

# Installation

Download the following file, extract the zip file and upload the content of the copy_this folder in the root directory of your osCommerce shop.
There is also a folder named changed_full:

Inside this folder you will find the file changed_full/admin/orders.php

Inside this file you will find some code between the marker "<!-- Paymill begin -->" at the beginning and "<!-- Paymill end -->"
at the end. Find the equivalent point at your catalog/admin/orders.php and paste the code between the paymill markers.

https://github.com/paymill/paymill-oscommerce/archive/master.zip

# Configuration

Afterwards enable PAYMILL in your shop backend and insert your test or live keys.

# In case of errors

In case of any errors check the PAYMILL log entry in the plugin config and 
contact the PAYMILL support (support@paymill.de).

# Notes about the payment process

The payment is processed when an order is placed in the shop frontend.

Fast Checkout: Fast checkout can be enabled by selecting the option in the PAYMILL Basic Settings. If any customer completes a purchase while the option is active this customer will not be asked for data again. Instead a reference to the customer data will be saved allowing comfort during checkout.

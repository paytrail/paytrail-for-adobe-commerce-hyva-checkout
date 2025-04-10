# Paytrail_PaymentServiceHyvaCheckout module

This module is created to support Paytrail payment service in Magento 2 Hyva theme with Hyva Checkout.

## Currently Supported Integration type
We are currently supporting only the "Redirect" integration type. So you need to set `Payment method selection 
on a separate page` to `Yes` in the Magento admin Paytrail payment method settings.

## Installation:

add the repository to your composer.json file with the command
```bash 
composer config repositories.paytrail_hyva_checkout vcs https://github.com/paytrail/paytrail-for-adobe-commerce-hyva-checkout.git
```

Then run the following command to install the module:
```bash 
composer require paytrail/paytrail-for-adobe-commerce-hyva-checkout
```

# Airwallex WHMCS Payment Gateway

### Requirements

PHP >= 7.4
WHMCS >= 8 (Tested on WHMCS 8 only)

### Installation

Copy all files into modules/gateways

### Configuration

Client ID: Airwallex Unique Client ID
API Key: Airwallex Client API Key
Webhook Secret Key: Airwallex Webhook Secret Key

Webhook: https://yoursystem.com/modules/gateways/airwallex/callback_alipay.php or https://yoursystem.com/modules/gateways/airwallex/callback_wechatpay.php

Subscribe Events: `Payment Intent: Succeeded`

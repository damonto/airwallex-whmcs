# Airwallex WHMCS Payment Gateway

### Requirements

PHP >= 7.4

WHMCS >= 8 (Tested on WHMCS 8 only)

### Installation

Copy all files to the `modules/gateways` directory.

### Configuration

Client ID: Airwallex Unique Client ID

API Key: Airwallex Client API Key

Webhook Secret Key: Airwallex Webhook Secret Key

#### Airwallex

Webhook: https://yoursystem.com/modules/gateways/airwallex/callback.php

Subscribe Events: `Payment Intent: Succeeded`

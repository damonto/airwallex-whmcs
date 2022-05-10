<?php

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

$payload = json_decode(file_get_contents('php://input'), true);
$gatewayConfig = getGatewayVariables('airwallexwechatpay');

if (!$gatewayConfig['type']) {
    die('Module Not Activated');
}

$hash = hash_hmac('sha256', $_SERVER['HTTP_X_TIMESTAMP'] . file_get_contents('php://input'), $gatewayConfig['webhookSecretKey']);
if ($hash !== $_SERVER['HTTP_X_SIGNATURE']) {
    die('Invalid callback');
}

if (($payload['name'] ?? null) === 'payment_intent.succeeded') {
    $intent = $payload['data']['object'];

    $invoiceId = checkCbInvoiceID($intent['merchant_order_id'], 'airwallexaliwechatpay');
    checkCbTransID($intent['id']);

    logTransaction('airwallexaliwechatpay', $payload, 'Success');

    addInvoicePayment(
        $intent['merchant_order_id'],
        $intent['id'],
        $intent['amount'],
        0,
        'airwallexaliwechatpay'
    );
}

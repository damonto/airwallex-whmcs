<?php

require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

$payload = json_decode(file_get_contents('php://input'), true);
$intent = $payload['data']['object'];

$gatewayConfig = getGatewayVariables($intent['metadata']['payment_method']);

if (!$gatewayConfig['type']) {
    die('Module Not Activated');
}

$hash = hash_hmac('sha256', $_SERVER['HTTP_X_TIMESTAMP'] . file_get_contents('php://input'), $gatewayConfig['webhookSecretKey']);
if ($hash !== $_SERVER['HTTP_X_SIGNATURE']) {
    die('Invalid callback');
}

if (($payload['name'] ?? null) === 'payment_intent.succeeded') {
    $invoiceId = checkCbInvoiceID($intent['merchant_order_id'], $intent['metadata']['payment_method']);
    checkCbTransID($intent['id']);

    logTransaction($intent['metadata']['payment_method'], $payload, 'Success');

    addInvoicePayment(
        $intent['merchant_order_id'],
        $intent['id'],
        $intent['metadata']['payment_method'] === 'airwallexfps' ? round($intent['amount'] * $gatewayConfig['exchangeRate'], 2) : $intent['amount'],
        0,
        $intent['metadata']['payment_method']
    );
}

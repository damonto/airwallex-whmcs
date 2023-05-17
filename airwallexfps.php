<?php

use Airwallex\Airwallex;

require __DIR__ . '/airwallex/Airwallex/vendor/autoload.php';

function airwallexfps_MetaData()
{
    return [
        'DisplayName' => 'Airwallex FPS',
        'APIVersion' => '1.1'
    ];
}

function airwallexfps_config()
{
    return [
        'FriendlyName' => [
            'Type' => 'System',
            'Value' => 'Airwallex FPS',
        ],
        'clientId' => [
            'FriendlyName' => 'Airwallex Client ID',
            'Type' => 'text',
            'Size' => 256,
            'Default' => '',
            'Description' => 'The airwallex API Client ID',
        ],
        'exchangeRate' => [
            'FriendlyName' => 'USD to HKD',
            'Type' => 'text',
            'Size' => 256,
            'Default' => 7.85,
            'Description' => 'USD to HKD exchange rate',
        ],
        'apiKey' => [
            'FriendlyName' => 'Airwallex API Key',
            'Type' => 'text',
            'Size' => 256,
            'Default' => '',
            'Description' => 'The airwallex API Key',
        ],
        'webhookSecretKey' => [
            'FriendlyName' => 'Airwallex Webhook Secret Key',
            'Type' => 'text',
            'Size' => 256,
            'Default' => '',
            'Description' => 'The airwallex webhook secret key',
        ]
    ];
}

function airwallexfps_link(array $params)
{
    $airwallex = new Airwallex($params['clientId'], $params['apiKey']);
    $intent  = $airwallex->intents()->create([
        'amount' => round($params['amount'] * $params['exchangeRate'], 2),
        'currency' => 'HKD',
        'descriptor' => $params['description'],
        'merchant_order_id' => $params['invoiceid'],
        'return_url' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        'metadata' => [
            'payment_method' => 'airwallexfps',
        ],
        'customer' => [
            'email' => $params['client']->email,
            'first_name' => $params['client']->firstname,
            'last_name' => $params['client']->lastname
        ]
    ]);

    $confirmed = $airwallex->intents()->confirm($intent['id'], 'fps', [
        'flow' => 'webqr'
    ]);

    return <<<HTML
        <script src="/modules/gateways/airwallex/qrcode.min.js"></script>
        <div style="width: 100%;">
            <div id="qrcode"> </div>
            <img class="logo" src="https://fps.hkicl.com.hk/images/fps/fps_color.svg" alt="FPS Logo">
        </div>

        <style>
            #qrcode img {
                margin: 0 auto;
                height: 150px;
            }

            .logo {
                height: 30px;
                margin-top: 10px;
            }

        </style>

        <script>
            new QRCode(document.getElementById("qrcode"), '{$confirmed["next_action"]["qrcode"]}');

            setInterval(() => {
                fetch("/modules/gateways/airwallex/invoice_status.php?invoiceid={$params['invoiceid']}", {
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                })
                .then(r => r.text())
                .then(r => {
                    if (r === 'Paid') {
                        window.location.reload(true)
                    }
                })
            }, 5000);
        </script>
    HTML;
}

function airwallexfps_refund(array $params)
{
    $airwallex = new Airwallex($params['clientId'], $params['apiKey']);

    try {
        $refund = $airwallex->refunds()->create([
            'amount' => $params['amount'],
            'payment_intent_id' => $params['transid']
        ]);

        return [
            // 'success' if successful, otherwise 'declined', 'error' for failure
            'status' => 'success',
            // Data to be recorded in the gateway log - can be a string or array
            'rawdata' => $refund,
            // Unique Transaction ID for the refund transaction
            'transid' => $params['transid'],
            // Optional fee amount for the fee value refunded
            'fees' => 0,
        ];
    } catch (Throwable $e) {
        return [
            // 'success' if successful, otherwise 'declined', 'error' for failure
            'status' => 'error',
            // Data to be recorded in the gateway log - can be a string or array
            'rawdata' => $e->getMessage(),
            // Unique Transaction ID for the refund transaction
            'transid' => $params['transid'],
            // Optional fee amount for the fee value refunded
            'fees' => 0,
        ];
    }
}

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
        'amount' => $params['amount'] * $params['exchangeRate'],
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
        <a href="{$confirmed['next_action']['url']}" target="_blank" style="display: block; background-color: #0171fd; padding: 10px; width: 180px; margin: 0 auto; border-radius: 4px; cursor: pointer;">
            <svg height="28" viewBox="0 0 100 28" width="100" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g fill="#fff" fill-rule="evenodd"><g transform="translate(0 1)"><path class="logo-color" d="m20.7087656 15.9839943c3.6219746 1.2231649 4.4435954 1.2892177 4.4435954 1.2892177v-13.15329907c0-2.24508252-1.8019585-4.06414821-4.0277954-4.06414821h-17.09532084c-2.22631211 0-4.029221 1.81906569-4.029221 4.06414821v17.23978167c0 2.2436569 1.80290889 4.063673 4.029221 4.063673h17.09532084c2.2258369 0 4.0277954-1.8200161 4.0277954-4.063673v-.16632s-6.5420787-2.7502202-9.8459069-4.3528322c-2.2158577 2.7499825-5.0741858 4.4184098-8.04133478 4.4184098-5.01858745 0-6.72265473-4.4286266-4.34641701-7.3449292.51820562-.63558 1.39946407-1.2421728 2.76708973-1.5824161 2.13958811-.5300856 5.54558426.3309769 8.73726526 1.3928113.5735664-1.0665864 1.0568448-2.2405681 1.4168088-3.4915322h-9.83640284v-1.00528562h5.07180984v-1.80100809h-6.14243549v-1.00504805h6.14243549v-2.56964413s0-.43290722.434808-.43290722h2.4791186v3.00255135h6.0730563v1.00504805h-6.0730563v1.80100809h4.9575242c-.4742496 1.96020012-1.1953657 3.76405942-2.0991961 5.34790102 1.5042457.5488561 2.8552393 1.0687249 3.8612378 1.4084929" transform="translate(0 .181764)"></path><path class="logo-color" d="m6.11351958 14.0176166c-.62868963.0632016-1.80789849.343332-2.45322012.9180865-1.93382649 1.7005032-.77695204 4.8092618 3.13632016 4.8092618 2.27406971 0 4.54742658-1.4664673 6.33227788-3.8146682-2.5392313-1.2500136-4.690937-2.1445777-7.01537792-1.9126801"></path><path class="logo-color" d="m54.6161881 3.9511694c0 1.31226486.9589536 2.19566171 2.2966417 2.19566171s2.2966417-.88339685 2.2966417-2.19566171c0-1.28707927-.9589536-2.19542411-2.2966417-2.19542411s-2.2966417.90834484-2.2966417 2.19542411"></path><path d="m48.205716 20.7346402h3.987641v-18.42398008h-3.987641z"></path><path d="m35.4095773 14.349425 2.3721985-8.20218997h.1012176l2.2462705 8.20218997zm5.8050435-11.40788933h-5.3507523l-5.9815803 17.79315213h3.6849386l1.0095624-3.482741h6.3348916l.958716 3.482741h4.7199242z"></path><path d="m54.9191043 20.7346402h3.987641v-13.52823184h-3.987641z"></path><path d="m100.57694 7.23209292.024948-.024948h-3.760495l-2.3721985 8.22737558h-.1261656l-2.7257474-8.22737558h-4.467593l5.3761755 13.57812788-2.2462705 4.1389922v.1012176h3.5081641z"></path><path d="m66.302212 18.4127654c-.4540536 0-.8833968-.0506088-1.362636-.20196v-7.3187932c.832788-.580932 1.5142248-.8584488 2.3721985-.8584488 1.4892768 0 2.6753761 1.1865744 2.6753761 3.7103618 0 3.2301721-1.7413705 4.6688402-3.6849386 4.6688402m2.5240249-11.48344617c-1.4638536 0-2.5995817.55527122-3.8866609 1.61520488v-1.33768807h-3.9881162v17.81833766h3.9881162v-4.4167466c.7569936.20196 1.463616.3031776 2.3218273.3031776 3.5585353 0 6.7639971-2.6250049 6.7639971-7.2943203 0-4.18912585-2.3223025-6.68796517-5.1991635-6.68796517"></path><path d="m82.379892 17.4537642c-1.0599336.5804569-1.6660513.8073649-2.3721985.8073649-.9591912 0-1.5648337-.6308281-1.5648337-1.6403905 0-.3784968.0755568-.756756.3782592-1.0599337.4797145-.4792392 1.4137201-.8330256 3.558773-1.337688zm3.987641-.10098v-5.6534547c0-3.07905851-1.8169273-4.77005779-5.0221514-4.77005779-2.0445481 0-3.457793.35331121-6.0321891 1.13572805l.7063848 3.10471934c2.3472505-1.0604088 3.3822362-1.51470006 4.467593-1.51470006 1.3125025 0 1.8927217.93376806 1.8927217 2.37243616v.10098c-4.5683354.8579736-5.9815803 1.337688-6.8649771 2.2213225-.6560137.655776-.9335305 1.5895441-.9335305 2.6749009 0 2.5998193 2.0191249 3.9878786 3.9120842 3.9878786 1.4132449 0 2.5487354-.5303232 4.088621-1.6912369l.2775168 1.4137201h3.987641z"></path></g></g></svg>
        </a>

        <script>
            setInterval(() => {
                fetch("/modules/gateways/airwallex/invoice_status.php?invoiceid={$params['invoiceid']}")
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

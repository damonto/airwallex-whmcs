<?php

namespace Airwallex\Intents;

use Airwallex\Http;

class Intents
{
    /**
     * The payment intents endpoints.
     *
     * @var  string[]
     */
    protected array $endpoints = [
        'create' => '/api/v1/pa/payment_intents/create',
        'confirm' => '/api/v1/pa/payment_intents/%s/confirm'
    ];

    /**
     * The http client.
     *
     * @var  Http
     */
    protected Http $http;

    /**
     * Create a new payment intents instance.
     *
     * @param   Http  $http
     *
     * @return  void
     */
    public function __construct(Http $http)
    {
        $this->http = $http;

        $this->http->withToken();
    }

    /**
     * Create a payment intent.
     *
     * @param   array  $payload
     *
     * @return  array
     */
    public function create(array $payload = [])
    {
        return $this->http->request('POST', $this->endpoints['create'], $payload);
    }

    /**
     * Confirm a payment intent.
     *
     * @param   string  $id
     * @param   string  $paymentMethod
     * @param   array   $payload
     *
     * @return  array
     */
    public function confirm(string $paymentId, string $paymentMethod, array $payload)
    {
        return $this->http->request('POST', sprintf($this->endpoints['confirm'], $paymentId), [
            'payment_method' => [
                'type' => $paymentMethod,
                $paymentMethod => $payload
            ]
        ]);
    }
}

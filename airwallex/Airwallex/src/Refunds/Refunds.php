<?php

namespace Airwallex\Refunds;

use Airwallex\Http;

class Refunds
{
    /**
     * The payment intents endpoints.
     *
     * @var  string[]
     */
    protected array $endpoints = [
        'create' => '/api/v1/pa/refunds/create',
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
     * Create a payment refund.
     *
     * @param   array  $payload
     *
     * @return  array
     */
    public function create(array $payload = [])
    {
        return $this->http->request('POST', $this->endpoints['create'], $payload);
    }
}

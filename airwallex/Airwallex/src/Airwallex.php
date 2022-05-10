<?php

namespace Airwallex;

use Airwallex\Intents\Intents;
use Airwallex\Refunds\Refunds;

class Airwallex
{
    /**
     * The http instance.
     *
     * @var Http
     */
    protected Http $http;

    /**
     * Create Airwallex instance.
     *
     * @param   string  $clientId
     * @param   string  $apiKey
     *
     * @return  void
     */
    public function __construct(string $clientId, string $apiKey)
    {
        $this->http = new Http($clientId, $apiKey);
    }

    /**
     * Payment Intents.
     *
     * @return Intents
     */
    public function intents()
    {
        return new Intents($this->http);
    }

    /**
     * Refunds.
     *
     * @return Intents
     */
    public function refunds()
    {
        return new Refunds($this->http);
    }
}

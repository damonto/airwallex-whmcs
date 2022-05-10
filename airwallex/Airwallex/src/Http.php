<?php

namespace Airwallex;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Http\Message\RequestInterface;

use function GuzzleHttp\Psr7\stream_for;

class Http
{
    /**
     * The airwallex production api endpoint.
     */
    private const AIRWALLEX_PRODUCTION = 'https://api.airwallex.com';

    /**
     * The airwallex client id.
     *
     * @var string
     */
    protected string $clientId;

    /**
     * The airwallex api key.
     *
     * @var string
     */
    protected string $apiKey;

    /**
     * Guzzle Http client.
     *
     * @var Client
     */
    protected Client $httpClient;

    /**
     * Guzzle http handler stack.
     *
     * @var HandlerStack
     */
    protected HandlerStack $stack;

    /**
     * Create the airwallex http request client.
     *
     * @param   string  $clientId
     * @param   string  $apiKey
     *
     * @return  void
     */
    public function __construct(string $clientId, string $apiKey)
    {
        $this->clientId =  $clientId;
        $this->apiKey = $apiKey;

        $this->setupGuzzlehttp();
    }

    /**
     * Setup Http client.
     *
     * @return  void
     */
    protected function setupGuzzlehttp()
    {
        $handler = new CurlHandler();
        $this->stack = HandlerStack::create($handler);

        $this->httpClient = new Client([
            'base_uri' => self::AIRWALLEX_PRODUCTION,
            'handler' => $this->stack,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-API-Key' => $this->apiKey,
                'X-Client-ID' => $this->clientId,
            ],
        ]);

        $this->addRequestId();
        $this->addLogger();
    }

    /**
     * Add logger to the http client.
     *
     * @return  void
     */
    protected function addLogger()
    {
        $logger = new Logger('Logger');
        $logger->pushHandler(new StreamHandler(sys_get_temp_dir() . '/airwallex.log', Logger::DEBUG));
        $this->stack->unshift(Middleware::log($logger, new MessageFormatter(MessageFormatter::DEBUG)));
    }

    /**
     * Add a token to each request.
     *
     * @return  void
     */
    public function withToken()
    {
        $token = (new AccessToken($this))->getToken();

        $this->stack->push(function (callable $handler) use ($token) {
            return function (RequestInterface $request, array $options) use ($handler, $token) {
                $request = $request->withHeader('Authorization', 'Bearer ' . $token);

                return $handler($request, $options);
            };
        });
    }

    /**
     * Add a request id to each request.
     *
     * @return  void
     */
    protected function addRequestId()
    {
        $this->stack->push(function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $body = $request->getBody()->getContents();
                if (is_string($body)) {
                    $body = json_decode($body, true);
                    $body['request_id'] = time() . uniqid();
                }

                $request = $request->withBody(stream_for(json_encode($body)));

                return $handler($request, $options);
            };
        });
    }

    /**
     * Send an HTTP request.
     *
     * @param   string  $method
     * @param   string  $uri
     * @param   array   $data
     *
     * @return  array
     */
    public function request(string $method, string $uri, array $data = [])
    {
        $response = $this->httpClient->request($method, $uri, [
            'json' => $data
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}

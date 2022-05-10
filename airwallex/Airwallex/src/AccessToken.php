<?php

namespace Airwallex;

use Carbon\Carbon;

class AccessToken
{
    /**
     * The airwallex http client.
     *
     * @var Http
     */
    protected Http $http;

    /**
     * The airwallex token cache dir.
     *
     * @var string
     */
    protected string $cacheDir;

    /**
     * Token airwallex token cache file name.
     *
     * @var string
     */
    protected string $tokenName = 'airwallex.access_token.json';

    /**
     * The airwallex authencation api endpoint.
     *
     * @return string
     */
    protected string $endpoint = '/api/v1/authentication/login';

    /**
     * Create AccessToken instance.
     *
     * @param   Http  $http
     *
     * @return  string
     */
    public function __construct(Http $http)
    {
        $this->http = $http;

        $this->cacheDir = sys_get_temp_dir();
    }

    /**
     * Get the airwallex access token path.
     *
     * @return string
     */
    protected function getTokenFile()
    {
        return $this->cacheDir . '/' . $this->tokenName;
    }

    /**
     * Set the access token and expires at into the cache file.
     *
     * @param   string  $token
     * @param   string  $expiresAt
     *
     * @return  void
     */
    public function setToken(string $token, string $expiresAt)
    {
        $payload = [
            'token' => $token,
            'expires_at' => $expiresAt,
        ];

        file_put_contents($this->getTokenFile(), json_encode($payload));
    }

    /**
     * Get the access token from the cache file.
     *
     * @return string
     */
    public function getToken()
    {
        if ($this->hasValidToken()) {
            return json_decode(file_get_contents($this->getTokenFile()), true)['token'];
        }

        $token = $this->http->request('POST', $this->endpoint);

        $this->setToken($token['token'], $token['expires_at']);

        return $token['token'];
    }

    /**
     * Check if the access token is valid.
     *
     * @return bool
     */
    protected function hasValidToken()
    {
        if (! file_exists($this->getTokenFile())) {
            return false;
        }

        $token = json_decode(file_get_contents($this->getTokenFile()));

        if (Carbon::now()->gt(Carbon::parse($token->expires_at))) {
            return false;
        }

        return true;
    }
}

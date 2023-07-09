<?php

namespace App\GrantTypes;

use GuzzleHttp\ClientInterface;

class ClientCredentials implements GrantTypeInterface
{
    /**
     * The token endpoint client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * Configuration settings.
     *
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(ClientInterface $client, array $config)
    {
        $this->client = $client;

        $required = ['token_uri', 'app_id', 'app_key'];

        if ($missing = $this->missing_keys($required, $config)) {
            $message = 'Parameters: ' . implode(', ', $missing) . ' are required.';

            throw new \InvalidArgumentException($message, 0);
        }

        $this->config = array_merge([
            'scope' => '',
        ], $config);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getToken($refreshToken = null)
    {
        $response = $this->client->request('POST', $this->config['token_uri'], [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'appId' => $this->config['appId'],
                'appKey' => $this->config['appKey'],
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
    /**
     * Get missing array keys.
     *
     * @param array $required
     * @param array $given
     *
     * @return array|null Missing keys
     */
    function missing_keys(array $required, array $given)
    {
        if ($this->is_associative($given)) {
            $given = array_keys($given);
        }

        return array_diff($required, $given);
    }

    /**
     * Is this an associative array?
     *
     * @link https://stackoverflow.com/a/173479/2732184 Source
     *
     * @param array $arr
     *
     * @return bool
     */
    function is_associative(array $arr)
    {
        if ([] === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
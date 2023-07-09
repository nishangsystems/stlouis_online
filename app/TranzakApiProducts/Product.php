<?php

namespace App\TranzakApiProducts;

use App\GrantTypes\ClientCredentials;
use App\GrantTypes\OAuth2Middleware;
use App\Repositories\MomoApiTokenRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;

abstract class Product
{
    /**
     * Product.
     *
     * @var string
     */
    const PRODUCT = null;

    /**
     * Configuration.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * Base URI.
     *
     * @var string
     */
    protected $baseUri;

    /**
     * Token URI.
     *
     * @var string
     */
    protected $tokenUri;


    /**
     * App ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * App Key.
     *
     * @var string
     */
    protected $appKey;

    /**
     * Callback URI.
     *
     * @var string
     */
    protected $callbackUri;

    /**
     * Currency Code.
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Environment.
     *
     * @var string
     */
    protected $environment;



    /**
     * Log file.
     *
     * @var string
     */
    protected $logFile;

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @param string $baseUri
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @return string
     */
    public function getTokenUri()
    {
        return $this->tokenUri;
    }

    /**
     * @param string $tokenUri
     */
    public function setTokenUri($tokenUri)
    {
        $this->tokenUri = $tokenUri;
    }

    /**
     * @return string $appId
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     */
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }

    /**
     * @return string
     */
    public function getCallbackUri()
    {
        return $this->callbackUri;
    }

    /**
     * @param string $callbackUri
     */
    public function setCallbackUri($callbackUri)
    {
        $this->callbackUri = $callbackUri;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @param string $logFile
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }

    /**
     * Constructor.
     *
     * @param array $headers
     * @param array $middleware
     * @param \GuzzleHttp\ClientInterface $client
     *
     * @throws \Exception
     */
    public function __construct($headers = [], $middleware = [], ClientInterface $client = null)
    {

        // Set defaults.
        $this->setConfigurations();

        if ($client) {
            $this->client = $client;
            return;
        }

        // Guzzle http request headers.
        $headers = array_merge([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], $headers);

        // Guzzle http client middleware.

        $handlerStack = HandlerStack::create();

        foreach ($middleware as $mw) {
            $handlerStack->push($mw);
        }

        $handlerStack->push($this->getAuthBroker($headers));

        $handlerStack = $this->append_log_middleware($handlerStack);

        $options = array_merge([
            'handler' => $handlerStack,
            'base_uri' => $this->baseUri,
            'headers' => $headers,
        ], config('app.mtn-momo.guzzle.options'));

        // Set http client.
        $this->client = new Client($options);
    }

    /**
     * Request access token.
     *
     * @return array
     */
    abstract public function getToken();

    /**
     * Setup default configurations.
     *
     * @uses \Illuminate\Contracts\Config\Repository
     *
     * @return void
     */
    private function setConfigurations()
    {
        $this->baseUri = config('app.tranzak.api.base_uri');
        $this->currencyCode = config('app.tranzak.currencyCode');
        $this->environment = config('app.tranzak.environment');
        $this->logFile = config('app.tranzak.log');
    }

    /**
     * Get authentication broker.
     * @param $headers array $headers HTTP request headers
     * @return OAuth2Middleware
     * @throws \Exception
     */
    protected function getAuthBroker($headers)
    {
        $handlerStack = HandlerStack::create();

        $handlerStack = $this->append_log_middleware($handlerStack);

        $options = array_merge([
            'base_uri' => $this->baseUri,
            'handler' => $handlerStack,
            'headers' => $headers,
            'json' => [
                'body',
            ],
        ], config('app.tranzak.guzzle.options'));

        // Authorization client - this is used to request OAuth access tokens
        $client = new Client($options);

        $config = [
            'app_id' => $this->appId,
            'app_key' => $this->appKey,
            'token_uri' => $this->tokenUri,
        ];

        // This grant type is used to get a new Access Token and,
        // Refresh Token when no valid Access Token or Refresh Token is available
        $clientCredGrant = new ClientCredentials($client, $config);

        // Create token repository
        $tokenRepo = new TokenRepository(static::PRODUCT);

        // Tell the middleware to use both the client and refresh token grants
        return new OAuth2Middleware($clientCredGrant, null, $tokenRepo);
    }

    /**
     * Add log middleware to handler stack.
     *
     * @see https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
     *
     * @param \GuzzleHttp\HandlerStack $handlerStack
     *
     * @throws \Exception
     *
     * @return \GuzzleHttp\HandlerStack
     */
    function append_log_middleware(HandlerStack $handlerStack)
    {
        $id = Str::random(10);

        $messageFormats = [
            "HTTP_OUT_{$id} [Request] {method} {target}" => 'info',
            "HTTP_OUT_{$id} [Request] [Headers] \n{req_headers}" => 'debug',
            "HTTP_OUT_{$id} [Request] [Body] {req_body}" => 'debug',
            "HTTP_OUT_{$id} [Response] HTTP/{version} {code} {phrase} Size: {res_header_Content-Length}" => 'info',
            "HTTP_OUT_{$id} [Response] [Headers] \n{res_headers}" => 'debug',
            "HTTP_OUT_{$id} [Response] [Body] {res_body}" => 'debug',
            "HTTP_OUT_{$id} [Error] {error}" => 'error',
        ];

        $logger = Container::getInstance()->get('log');

        collect($messageFormats)->each(function ($level, $format) use ($logger, $handlerStack) {
            $messageFormatter = new MessageFormatter($format);

            $logMiddleware = Middleware::log($logger, $messageFormatter, $level);

            $handlerStack->unshift($logMiddleware);
        });

        return $handlerStack;
    }
    /**
     * Strip all symbols from a string.
     *
     * @see https://stackoverflow.com/a/16791863/2732184 Source
     *
     * @param string  $str
     *
     * @return string
     */
    function alphanumeric($str): string
    {
        return preg_replace('/[^\p{L}\p{N}\s]/u', '', $str);
    }
}
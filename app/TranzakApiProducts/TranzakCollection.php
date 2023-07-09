<?php

namespace App\TranzakApiProducts;

use App\Exceptions\CollectionRequestException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Ramsey\Uuid\Uuid;

class TranzakCollection extends TranzakProduct
{

    /**
     * TranzakProduct.
     *
     * @var string
     */
    const PRODUCT = 'collection';


    /**
     * @param $headers
     * @param $middleware
     * @param ClientInterface|null $client
     * @throws \Exception
     */
    public function __construct($headers = [], $middleware = [], ClientInterface $client = null)
    {

        $this->countryCode = config('app.tranzak.country_code');
        $this->currencyCode = config('app.tranzak.currency_code');
        $this->appId = config('app.tranzak.products.collection.id');
        $this->appKey = config('app.tranzak.products.collection.secret');
        $this->callbackUri = config('app.tranzak.products.collection.callback_uri');

        $this->tokenUri = config('app.tranzak.products.collection.token_uri');
        $this->redirectPaymentUri = config('app.tranzak.products.collection.redirect_payment_uri');
        $this->mobileWalletUri = config('app.tranzak.products.collection.mobile_wallet_uri');
        $this->transactionStatusUri = config('app.tranzak.products.collection.transaction_status_uri');
        $this->requestDetailsUri = config('app.tranzak.products.collection.request_details_uri');

        parent::__construct($headers, $middleware, $client);
    }

    /**
     * @param $amount
     * @param $description
     * @param $mobileWalletNumber
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestToPayWithMobileWallet($amount, $description = '',$mobileWalletNumber = '')
    {
        $mchTransactionRef = Uuid::uuid4()->toString();
        $headers = [];
        if ( !str_starts_with($mobileWalletNumber, $this->countryCode) ){
            $mobileWalletNumber = $this->countryCode + $mobileWalletNumber;
        }
        $options =  [
            'headers' => $headers,
            'json' =>  [
                'amount' => $amount,
                'currencyCode' => $this->currency,
                'description' => $description,
                'mchTransactionRef' => $mchTransactionRef,
                'returnUrl'=> $this->callbackUri,
                'mobileWalletNumber'=> $mobileWalletNumber
            ],
        ];

        try {

            $response =  $this->client->request('POST', $this->transactionUri,$options);
            $responseBody = json_decode($response->getBody(), true);
            if (!$responseBody->success){
                throw new CollectionRequestException('Request to pay transaction - unsuccessful.');
            }
            return $responseBody;
        } catch (RequestException $ex) {

            throw new CollectionRequestException('Request to pay transaction - unsuccessful.', 0, $ex);
        }
    }

    /**
     * @param $amount
     * @param $description
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestToPayWithRedirectPayment($amount, $description = '')
    {
        $mchTransactionRef = Uuid::uuid4()->toString();
        $headers = [];
        $options =  [
            'headers' => $headers,
            'json' =>  [
                'amount' => $amount,
                'currencyCode' => $this->currency,
                'description' => $description,
                'mchTransactionRef' => $mchTransactionRef,
                'returnUrl'=> $this->callbackUri
            ],
        ];
        try {

            $response =  $this->client->request('POST', $this->transactionUri,$options);
            $responseBody = json_decode($response->getBody(), true);
            if (!$responseBody->success){
                throw new CollectionRequestException('Request to pay transaction - unsuccessful.');
            }
            return $responseBody;
        } catch (RequestException $ex) {

            throw new CollectionRequestException('Request to pay transaction - unsuccessful.', 0, $ex);
        }
    }

    /**
     * @param $transactionId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTransactionStatus($transactionId)
    {
        $transactionStatusUri = str_replace('{TRANSACTION_ID}', $transactionId, $this->transactionStatusUri);

        try {
            $response = $this->client->request('GET', $transactionStatusUri, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
            $responseBody = json_decode($response->getBody(), true);
            if (!$responseBody->success){
                throw new CollectionRequestException('Unable to get request details');
            }
            return $responseBody->status;
        } catch (RequestException $ex) {
            throw new CollectionRequestException('Unable to get transaction status.', 0, $ex);
        }
    }
    public function getRequestDetails($requestId)
    {
        $transactionStatusUri = str_replace('{REQUEST_ID}', $requestId, $this->requestDetailsUri);

        try {
            $response = $this->client->request('GET', $transactionStatusUri, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            if (!$responseBody->success){
                throw new CollectionRequestException('Unable to get request details');
            }
           return $responseBody;

        } catch (RequestException $ex) {
            throw new CollectionRequestException('Unable to get request.', 0, $ex);
        }
    }

    /**
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getToken()
    {
        try {
            $response = $this->client->request('POST', $this->tokenUri, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'appId' => $this->appId,
                    'appKey' => $this->appKey,
                ],
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $ex) {
            throw new CollectionRequestException('Unable to get token.', 0, $ex);
        }
    }

}
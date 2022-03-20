<?php

namespace Wameed\UrwayPaymentGateway;


use Exception;

class Urway extends Guzzle
{
    /**
     * @var string
     */
    protected $endpoint = 'URWAYPGService/transaction/jsonProcess/JSONrequest';

    /**
     * Request method.
     *
     * @var string
     */
    protected $method = 'POST';

    /**
     * Store request attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @return $this
     */
    public function setTrackId($trackId)
    {
        $this->attributes['trackid'] = $trackId;
        return $this;
    }

    /**
     * @return $this
     */
    public function setCustomerEmail($email)
    {
        $this->attributes['customerEmail'] = $email;
        return $this;
    }

    /**
     * @return $this
     */
    public function setCustomerIp($ip)
    {
        $this->attributes['merchantIp'] = $ip;
        return $this;
    }

    /**
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->attributes['currency'] = $currency;
        return $this;
    }

    /**
     * @return $this
     */
    public function setCountry($country)
    {
        $this->attributes['country'] = $country;
        return $this;
    }

    /**
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->attributes['amount'] = $amount;
        return $this;
    }

    /**
     * @return $this
     */
    public function setRedirectUrl($url)
    {
        $this->attributes['udf2'] = $url;
        return $this;
    }

    /**
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function mergeAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @param mixed $key
     *
     * @return boolean
     */
    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @param mixed $key
     *
     * @return Urway
     */
    public function removeAttribute($key)
    {
        $this->attributes = array_filter($this->attributes, function ($name) use ($key) {
            return $name !== $key;
        }, ARRAY_FILTER_USE_KEY);

        return $this;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function pay()
    {
        // set `terminal_id`, and `password` .
        $this->setAuthAttributes();

        // generate request
        $this->generateRequestHash();

        try {
            $response = $this->guzzleClient->request(
                $this->method,
                $this->getEndPointPath(),
                [
                    'json' => $this->attributes,
                ]
            );

            return new Response((string)$response->getBody());
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $transaction_id
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException|Exception
     */
    public function find(string $transaction_id)
    {
        // set `terminal_id`, and `password` now.
        $this->setAuthAttributes();

        // As requestHas for paying request is different from requestHash for find request.
        $this->generateFindRequestHash();

        $this->attributes['transid'] = $transaction_id;

        try {
            $response = $this->guzzleClient->request(
                $this->method,
                $this->getEndPointPath(),
                [
                    'json' => $this->attributes,
                ]
            );

            return new Response((string)$response->getBody());
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return void
     */
    protected function generateRequestHash()
    {
        $this->generateHash();
        $this->attributes['action'] = '1'; // action is always 1
    }

    /**
     * Security Check API For transaction performed authorization
     * @return void
     */
    protected function generateFindRequestHash()
    {
        $this->generateHash();
        $this->attributes['action'] = '10'; // action is always 1
    }

    /**
     * @return void
     */
    protected function setAuthAttributes()
    {
        $this->attributes['terminalId'] = config('urway.auth.terminal_id');
        $this->attributes['password'] = config('urway.auth.password');
    }

    /**
     *
     * Create SHA256 Hash with below mention Parameters.Merchant needs to form the below hash sequence before posting the transaction to Urway.
     * Below is the SHA 256 Hash creation format :
     * Hash Sequence :- trackid|Terminalid|password|secret_key|amount|currency_code
     * Note : Terminalid, password, secret_key will be provided by Urway
     *
     * @return void
     */
    protected function generateHash(): void
    {

        $requestHash = $this->attributes['trackid'] . '|' . config('urway.auth.terminal_id') . '|' . config('urway.auth.password') . '|' . config('urway.auth.merchant_key') . '|' . $this->attributes['amount'] . '|' . $this->attributes['currency'];
        $this->attributes['requestHash'] = hash('sha256', $requestHash);
    }
}
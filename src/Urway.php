<?php

namespace Wameed\UrwayPaymentGateway;


use Exception;

class Urway extends Guzzle
{
    /**
     * @var string
     */
    protected $endpoint;

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
    public function setMerchantIp($ip)
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
     * the language of the payment page
     * @return $this
     */
    public function setPaymentPageLanguage($locale)
    {
        $this->attributes['udf3'] = $locale;
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
     * @return UrwayResponse
     * @throws Exception
     */
    public function pay()
    {
        // set `terminal_id`, and `password` .
        $this->setAuthAttributes();

        // generate request
        $this->generateRequestHash();

        // set setMerchantIp if not set
        $this->_setMerchantIp();

        try {
            $response = $this->guzzleClient->request(
                $this->method,
                $this->getEndPointPath(),
                [
                    'json' => $this->attributes,
                ]
            );

            return new UrwayResponse((string)$response->getBody());
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $transaction_id
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException|Exception
     */
    public function verify(string $transaction_id)
    {
        // set `terminal_id`, and `password` now.
        $this->setAuthAttributes();

        // As requestHas for paying request is different from requestHash for find request.
        $this->generateFindRequestHash();

        // set setMerchantIp if not set
        $this->_setMerchantIp();

        $this->attributes['transid'] = $transaction_id;

        try {
            $response = $this->guzzleClient->request(
                $this->method,
                $this->getEndPointPath(),
                [
                    'json' => $this->attributes,
                ]
            );

            return new UrwayResponse((string)$response->getBody());
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

    /**
     * return the server ip address
     * @return mixed|string
     * @throws Exception
     */
    protected function _getServerIP()
    {
        $ip_address = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip_address = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        } else if (isset($_SERVER['SERVER_ADDR'])) {
            $ip_address = $_SERVER['SERVER_ADDR'];
        } else {
            throw new Exception('Unable to get server ip address');
        }

        return $ip_address;
    }


    /**
     * set setMerchantIp attribute if not set
     * @return void
     */
    protected function _setMerchantIp()
    {
        if (!$this->hasAttribute('merchantIp')) {
            $this->attributes['merchantIp'] = $this->_getServerIP();
        }
    }
}
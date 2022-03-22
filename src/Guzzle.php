<?php

namespace Wameed\UrwayPaymentGateway;

use GuzzleHttp\Client;

class Guzzle
{
    /**
     * Store guzzle client instance.
     *
     * @var Urway
     */
    protected $guzzleClient;

    /**
     * URWAY payment base path.
     *
     * @var string
     */
    protected $basePath ;

    /**
     * Store URWAY payment endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * BaseService Constructor.
     */
    public function __construct()
    {
        $this->guzzleClient = new Client();
        $this->basePath = config('urway.url.base');
        $this->endpoint = config('urway.url.payment');
    }

    /**
     * @return string
     */
    public function getEndPointPath()
    {
        return $this->basePath . '/' . $this->endpoint;
    }
}
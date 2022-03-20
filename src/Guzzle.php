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
    protected $basePath = 'https://payments-dev.urway-tech.com';

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
    }

    /**
     * @return string
     */
    public function getEndPointPath()
    {
        return $this->basePath . '/' . $this->endpoint;
    }
}
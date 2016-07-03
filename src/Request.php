<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

use GuzzleHttp\Client;

abstract class Request
{
    /** @var Client */
    protected $client;

    /** @var array */
    protected $config;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function createRequest($url, $method = 'GET')
    {
        return $this->client->createRequest( $method, $url );
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }
}

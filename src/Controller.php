<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;

class Controller
{
    /** @var string */
    protected $baseCacheKey;

    /** @var StorageInterface */
    protected $cache;

    /** @var Client */
    protected $client;

    /** @var string */
    protected $domain;

    /** @var ServiceInterface[] */
    protected $services;


    public function __construct($baseCacheKey, $cache, $client, $domain, $services)
    {
        $this->baseCacheKey = $baseCacheKey;
        $this->cache = $cache;
        $this->client = $client;
        $this->domain = $domain;
        $this->services = $services;
    }

    private function isValidDomain($url)
    {
        if ($this->domain) {
            $parsed = parse_url($url);

            if ($parsed["host"] != $this->domain) {
                return false;
            }
        }

        return true;
    }

    public function get($url)
    {

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Changes in configuration will invalidate the Cache
        $cache_key = md5($url.$this->baseCacheKey);

        if ($this->cache->contains($cache_key)) {
            return json_decode($this->cache->fetch($cache_key), true);
        }

        if (!$this->isValidDomain($url)) {
            return null;
        }

        $requests = array_map(
            function ($service) use ($url) {
                return $service->getRequest($url);
            },
            $this->services
        );

        $results = $this->client->send($requests);

        $counts = array();
        $i = 0;
        foreach ($this->services as $service) {
            if (method_exists($results[$i], "json")) {
                try {
                    $counts[ $service->getName() ] = intval($service->extractCount($results[$i]->json()));
                } catch (\Exception $e) {
                    // Skip service if broken
                }
            }
            $i++;
        }

        $this->cache->save($cache_key, json_encode($counts));

        return $counts;
    }

}
<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\Message\RequestInterface;
use Doctrine\Common\Cache\Cache as CacheInterface;
use Psr\Log\LoggerInterface;

class BackendManager
{
    /** @var string */
    protected $baseCacheKey;

    /** @var CacheInterface */
    protected $cache;

    /** @var Client */
    protected $client;

    /** @var array */
    protected $domains = array();

    /** @var ServiceInterface[] */
    protected $services;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param string             $baseCacheKey
     * @param CacheInterface     $cache
     * @param ClientInterface    $client
     * @param array              $domains
     * @param ServiceInterface[] $services
     */
    public function __construct($baseCacheKey, $cache, $client, $domains, $services)
    {
        $this->baseCacheKey = $baseCacheKey;
        $this->cache = $cache;
        $this->client = $client;
        $this->domains = $domains;
        $this->services = $services;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    private function isValidDomain($url)
    {
        if (!empty($this->domains)) {
            $parsed = parse_url($url);

            return in_array($parsed['host'], $this->domains, true);
        }

        return true;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $url
     *
     * @return array|mixed|null
     */
    public function get($url)
    {
        // Changing configuration invalidates the cache
        $cacheKey = md5($url.$this->baseCacheKey);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        if ($this->cache->contains($cache_key)) {
            return json_decode($this->cache->fetch($cache_key), true);
        }

        if (!$this->isValidDomain($url)) {
            return null;
        }

        $requests = array_map(
            function ($service) use ($url) {
                /* @var ServiceInterface $service */
                return $service->getRequest($url);
            },
            $this->services
        );

        $results = Pool::batch($this->client, $requests);

        $counts = array();
        $i = 0;
        foreach ($this->services as $service) {
            if ($results[$i] instanceof TransferException) {
                if ($this->logger !== null) {
                    $this->logger->warning($results[$i]->getMessage(), array('exception' => $results[$i]));
                }
            }
            else {
                try {
                    $content = $service->filterResponse($results[$i]->getBody()->getContents());
                    $counts[$service->getName()] = (int) $service->extractCount(json_decode($content, true));
                } catch (\Exception $e) {
                    if ($this->logger !== null) {
                        $this->logger->warning($e->getMessage(), array('exception' => $e));
                    }
                }
            }
            ++$i;
        }

        $this->cache->save($cache_key, json_encode($counts));

        return $counts;
    }

}
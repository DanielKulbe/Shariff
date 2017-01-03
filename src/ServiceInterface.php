<?php
namespace Bolt\Extension\DanielKulbe\Shariff;

use GuzzleHttp\Message\RequestInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * @param string $url
     *
     * @return RequestInterface
     */
    public function getRequest($url);

    /**
     * @param array $data
     *
     * @return int
     */
    public function extractCount(array $data);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $content
     *
     * @return string
     */
    public function filterResponse($content);

    /**
     * @param array $config
     */
    public function setConfig(array $config);
}
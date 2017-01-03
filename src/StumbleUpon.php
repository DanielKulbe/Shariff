<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

/**
 * Class StumbleUpon.
 */
class StumbleUpon extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'stumbleupon';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest($url)
    {
        return new \GuzzleHttp\Message\Request(
            'GET',
            'https://www.stumbleupon.com/services/1.01/badge.getinfo?url='.urlencode($url)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data)
    {
        return (isset($data['result']['views'])) ? $data['result']['views'] + 0 : 0;
    }
}

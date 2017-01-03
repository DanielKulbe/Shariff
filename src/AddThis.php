<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

/**
 * Class AddThis.
 */
class AddThis extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'addthis';
    }
    /**
     * {@inheritdoc}
     */
    public function getRequest($url)
    {
        return new \GuzzleHttp\Message\Request(
            'GET',
            'http://api-public.addthis.com/url/shares.json?url='.urlencode($url)
        );
    }
    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data)
    {
        return isset($data['shares']) ? $data['shares'] : 0;
    }
}
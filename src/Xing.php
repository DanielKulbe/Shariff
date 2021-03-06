<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

/**
 * Class Xing.
 */
class Xing extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'xing';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest($url)
    {
        return new \GuzzleHttp\Message\Request(
            'POST',
            'https://www.xing-share.com/spi/shares/statistics?url='.urlencode($url)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data)
    {
        return isset($data['share_counter']) ? $data['share_counter'] : 0;
    }
}

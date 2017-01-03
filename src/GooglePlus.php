<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

use GuzzleHttp\Stream;

/**
 * Class GooglePlus.
 */
class GooglePlus extends Request implements ServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'googleplus';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRequest($url)
    {
        $gPlusUrl = 'https://clients6.google.com/rpc?key=' . (isset($this->config['dev_key']) ? $this->config['dev_key'] : 'AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ');

        $json = array(
            'method' => 'pos.plusones.get',
            'id'     => 'p',
            'params' => array(
                'nolog'   => 'true',
                'id'      => $url,
                'source'  => 'widget',
                'userId'  => '@viewer',
                'groupId' => '@self'
            ),
            'jsonrpc'    => '2.0',
            'key'        => 'p',
            'apiVersion' => 'v1'
        );

        $body = Stream\Utils::create(json_encode($json));

        return new \GuzzleHttp\Message\Request('POST', $gPlusUrl, array(), $body);
    }

    /**
     * {@inheritdoc}
     */
    public function extractCount(array $data)
    {
        if (!empty($data['result']['metadata']['globalCounts']['count'])) {
            return $data['result']['metadata']['globalCounts']['count'];
        }

        return 0;
    }
}

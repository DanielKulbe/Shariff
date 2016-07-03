<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

class GooglePlus extends Request implements ServiceInterface
{

    public function getName()
    {
        return 'googleplus';
    }

    public function getRequest($url)
    {
        $key = isset($this->config['dev_key']) ? $this->config['dev_key'] : 'AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ';

        $request = $this->createRequest('https://clients6.google.com/rpc?key=' . $key, 'POST');
        $request->setBody(json_encode(array(
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
        )));

        return $request;
    }

    public function extractCount($data)
    {
        return $data['result']['metadata']['globalCounts']['count'];
    }
}

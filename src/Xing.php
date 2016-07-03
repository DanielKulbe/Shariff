<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

class Xing extends Request implements ServiceInterface
{

    public function getName()
    {
        return 'xing';
    }

    public function getRequest($url)
    {
        $request = $this->createRequest('https://www.xing-share.com/spi/shares/statistics', 'POST');
        $request->setPostField('url', $url);
        return $request;
    }

    public function extractCount($data)
    {
        return $data['share_counter'];
    }
}

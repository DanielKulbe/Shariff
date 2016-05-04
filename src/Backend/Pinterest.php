<?php

namespace Bolt\Extension\DanielKulbe\Shariff\Backend;

use Guzzle\Common\Event;
use Guzzle\Stream\Stream;
use GuzzleHttp\Message\Response;

class Pinterest extends Request implements ServiceInterface
{

    public function getName()
    {
        return 'pinterest';
    }

    public function getRequest($url)
    {
        $url = 'http://api.pinterest.com/v1/urls/count.json?callback=x&url='.urlencode($url);
        $request = $this->createRequest($url);
        $request->getEventDispatcher()->addListener('request.complete', function (Event $e) {
            // Stripping the 'callback function' from the response
            $body = $e['request']->getResponse()->getBody(true);
            $e['request']->setResponse(new Response(200, array(), mb_substr($body, 2, mb_strlen($body) - 3)));
        });
        return $request;
    }

    public function extractCount($data)
    {
        return $data['count'];
    }
}

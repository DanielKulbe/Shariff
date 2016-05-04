<?php

namespace Bolt\Extension\DanielKulbe\Shariff;

use Silex;
use Silex\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Bolt\Extension\DanielKulbe\Shariff\Backend\Controller;
use Bolt\Extension\DanielKulbe\Shariff\Backend\Service;

class Shariff
{
    /** @var  Bolt */
    protected $app;

    /** @var  Backend */
    protected $shariff;

    public function __construct (Application $app)
    {
        $this->app = $app;


        $config = $this->app['extensions.' . Extension::NAME]->config['server'];

        $services = array_map(function ($service) {
            if ( in_array($service['name'], Extension::getAvailableCounter()) ) return $service['name'];
            return false;
        }, $this->app['extensions.' . Extension::NAME]->config['services']);

        /** @var \Guzzle\Service\Client $client */
        $client = $this->app['guzzle.client'];
        if(isset($config['guzzle'])) $client->configureDefaults($config['guzzle']);

        $service = new Service($client);

        $this->shariff = new Controller(
            md5(json_encode($config)),
            $this->app['cache'],
            $client,
            $config['domain'],
            $service->getServicesByName($services, $config)
        );
    }

    public function shariff ()
    {
        $url = $this->app['request']->get('url');
        return $this->app->json($this->shariff->get($url));
    }
}
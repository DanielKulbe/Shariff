<?php
// Shariff extension for Bolt

namespace Bolt\Extension\DanielKulbe\Shariff;

use Bolt\Application;
use Bolt\BaseExtension;

class Extension extends BaseExtension
{
    /**
     * Extension name
     *
     * @var string
     */
    const NAME = 'Shariff';


    /**
     * Backend
     *
     * @var  Backend
     */
    protected $shariff;

    /**
     * Available themes
     *
     * @var array
     */
    protected static $THEMES = array(
        'standard', 'white', 'custom'
    );

    /**
     * Available counter backend implementations
     *
     * @var array
     */
    protected static $COUNTER = array(
        'Facebook', 'GooglePlus', 'LinkedIn', 'Pinterest', 'Reddit', 'StumbleUpon', 'Xing'
    );


    /**
     * Add Twig function, initialize Shariff backend
     *
     * @return void
     */
    public function initialize()
    {
        // Register Shariff Backend
        $counters = Extension::getAvailableCounter();
        $services = array_map(function ($service) use ($counters) {
            if ( in_array($service['name'], $counters) ) return $service['name'];
            return false;
        }, $this->config['services']);

        /** @var \Guzzle\Service\Client $client */
        $client = $this->app['guzzle.client'];
        if(isset($this->config['server']['guzzle'])) $client->configureDefaults($this->config['server']['guzzle']);

        $service = new Service($client);

        $this->shariff = new Controller(
            md5(json_encode($this->config['server'])),
            $this->app['cache'],
            $client,
            $this->config['server']['domain'],
            $service->getServicesByName($services, $this->config['server'])
        );

        // Register service route
        $this->app->match('/shariff/counter', array($this, 'counter'));

        // Add Extension template path, register Twig function "shariff", add script and stylesheet
        if ($this->app['config']->getWhichEnd() == 'frontend') {
            $this->app['twig.loader.filesystem']->addPath(__DIR__ . '/assets', 'Shariff');
            $this->addTwigFunction('shariff', 'shariff');

            if ($this->config['fontawesome']) {
                $this->addCss('assets/font-awesome.min.css');
            }

            if ($this->config['theme'] != 'custom') {
                $this->addCss('assets/shariff.min.css');
            }

            $this->addJavascript('assets/shariff.min.js', true);
        }
    }


    /**
     * Get the extension's human readable name
     *
     * @return string
     */
    public function getName()
    {
        return Extension::NAME;
    }


    /**
     * Set the defaults for configuration parameters
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return array(
            'count' => false,
            'fontawesome' => true,
            'theme' => 'standard',
            'orientation' => 'horizontal',
            'client' => array(),
            'services' => array(),
            'extra' => array(),
            'server' => array(
                'domain' => $this->app['paths']['hostname']
            ),
        );
    }


    /**
     * Receive the supported backend classes
     *
     * @return array
     */
    public static function getAvailableCounter ()
    {
        return Extension::$COUNTER;
    }


    /**
     * Shariff "shariff" Twig function handler
     *
     * @param $config  array  custom instance client setup
     * @return Twig_Markup    Rendered html
     */
    public function shariff (array $config = array())
    {
        $twigValues = array(
            'count' => $this->config['count'] == true ? true : false,
            'theme' => in_array($this->config['theme'], Extension::$THEMES) ? $this->config['theme'] : 'standard',
            'orientation' => $this->config['orientation'] == 'vertical' ? 'vertical' : 'horizontal',
            'config' => array_merge($this->config['client'], $config, array('backend' => $this->config['count'] == true ? '/shariff/counter' : null)),
            'services' => $this->config['services'],
            'extra' => $this->config['extra']
        );

        $str = $this->app['render']->render('@Shariff/shariff.twig', $twigValues);

        return new \Twig_Markup($str, 'UTF-8');
    }

    public function counter ()
    {
        \Symfony\Component\VarDumper\VarDumper::dump(debug_backtrace($this->app['request']));
        return $this->app->json(
            $this->shariff->get($this->app['request']->get('url'))
        );
    }
}

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
        'Facebook', 'GooglePlus', 'LinkedIn', 'Pinterest', 'Reddit', 'StumbleUpon', 'Twitter', 'Xing'
    );


    /**
     * Add Twig function, initialize Shariff backend
     *
     * @return void
     */
    public function initialize()
    {
        // Register service route
        $this->app->match('/shariff', array(new Shariff($this->app), 'shariff'));

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
    public static function getAvailableCounter () {
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
            'config' => array_merge($this->config['client'], $config, array('backend' => $this->config['count'] == true ? '/shariff' : null)),
            'services' => $this->config['services'],
            'extra' => $this->config['extra']
        );

        $str = $this->app['render']->render('@Shariff/shariff.twig', $twigValues);

        return new \Twig_Markup($str, 'UTF-8');
    }
}

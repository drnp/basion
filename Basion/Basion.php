<?php
/**
 * Basion RESTful toolkit
 *
 * @link        http://bsgroup.org/basion
 * @author      Dr.NP <np@bsgroup.org>
 */

namespace Basion;

/**
 *** Require ***
 *** Temp ***
 */
require 'Interfaces/FrameworkProviderInterface.php';
require 'FrameworkProviders/ProviderBase.php';
require 'FrameworkProviders/SilexProvider.php';
require 'Interfaces/MiddleWareInterface.php';
require 'MiddleWares/ContentType.php';
require 'MiddleWares/ApiVersion.php';
/**
 *** Require ***
 */

/**
 * Main processor
 *
 * @package     Basion
 * @since       1.0.0
 */
class Basion
{
    /**
     * Current version
     *
     * @var string
     */
    const VERSION       = '^1.0.0';

    /**
     * Supported frameworks
     *
     * @var array
     */
    private $supported_frameworks = [
        'silex'         => '', 
        'slim'          => '', 
        'yii2'          => '', 
        'laravel'       => '', 
        'symfony'       => ''
    ];

    /**
     * Supported middlewares
     *
     * @var array
     */
    private $supported_middlewares = [
        'ContentType'   => '', 
        'Authorization' => '', 
        'ApiVersion' => ''
    ];

    /**
     * Framework provider
     *
     * @var object
     */
    private $provider       = null;

    /**
     * Original framework object
     *
     * @var object
     */
    private $app            = null;

    /**
     * Framework name
     *
     * @var string
     */
    private $framework      = '';

    /**
     * Enabled middlewares
     *
     * @var array
     */
    private $middlewares    = [];

    /**
     * 

    /**
     * Constuct
     */
    function __construct($framework)
    {
        $framework = \trim(\strtolower($framework));
        if (!isset($this->supported_frameworks[$framework]))
        {
            throw new \InvalidArgumentException('Unsupported framework: ' . $framework);
        }

        $this->framework = $framework;

        return;
    }

    /**
     * @brief Init framework
     *
     * @param string    Framework
     *
     * @return object   Instance of framework
     */
    public function init($config = [])
    {
        // Parse config
        if (\is_array($config))
        {
            if (isset($config['middlewares']) && \is_array($config['middlewares']))
            {
                // Middlewares
                $this->middlewares = \array_intersect_key($config['middlewares'], $this->supported_middlewares);
            }
        }

        // Framework class
        $env_variable = 'BASION_FRAMEWORK_' . \strtoupper($this->framework) . '_DIR';
        $framework_path = \getenv($env_variable);
        if (!$framework_path)
        {
            $framework_path = $this->supported_framework[$this->framework];
        }

        if (!$framework_path)
        {
            throw new \InvalidArgumentException('Framework ' . $framework . ' not specified');
        }

        $c = 'Basion\\FrameworkProviders\\' . \ucfirst($this->framework) . 'Provider';
        try
        {
            $provider = new $c($framework_path);
            $provider->initFramework();
        }
        catch (Exception $e)
        {
            throw new \RuntimeException('Framework ' . $framework . ' initialize failed');
        }

        $this->app = $provider->getApp();
        $this->provider = $provider;
        if (sizeof($this->middlewares) > 0)
        {
            $this->_setMiddleWares();
        }

        return $provider;
    }

    /**
     * Start application
     */
    public function start()
    {
        return $this->provider->startFramework();
    }

    /**
     * Get framework provider
     *
     * @return mixed    Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Get framework instance from provider
     *
     * @return mixed    Instance
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Set provider's structrurized data
     *
     * @param mixed     Data
     */
    public function data($data)
    {
        //$this->provider->setStructurizedData($data);
        $this->provider['structurized_data'] = $data;
    }

    /**
     * @brief Set middlewares
     *
     * @return boolean  Status
     */
    private function _setMiddleWares()
    {
        $configs = [];
        try
        {
            foreach ($this->middlewares as $middleware => $config)
            {
                $c = 'Basion\\MiddleWares\\' . $middleware;
                $configs[$middleware] = $config;
                $this->provider->setBeforeMiddleWare([$c, 'before']);
                $this->provider->setAfterMiddleWare([$c, 'after']);
                $this->provider->setAbortMiddleWare([$c, 'abort']);
                $this->provider->setFinishMiddleWare([$c, 'finish']);
            }
        }
        catch (Exception $e)
        {
            throw new \RuntimeException('Middleware load failed');
        }

        $this->provider['middlewares'] = ['config' => $configs];
    }
}

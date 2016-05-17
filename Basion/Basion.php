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
require 'MiddleWares/DataEnvelope.php';
require 'MiddleWares/Authorization.php';
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
        'ApiVersion'    => 0,
        'Authorization' => 10, 
        'ContentType'   => 100, 
        'DataEnvelope'  => 80
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

        // HTTP request method
        $provider['http_request_method'] = \strtolower(\filter_input(\INPUT_SERVER, 'REQUEST_METHOD'));

        // HTTP status
        $provider['http_status_code'] = 200;

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
     * Set / Get provider's input data
     *
     * @param mixed     Data
     *
     * @return mixed    Current HTTP input data
     */
    public function input($data = null)
    {
        if ($data)
        {
            $this->provider['input_data'] = $data;
        }

        return $this->provider['input_data'];
    }

    /**
     * Set provider's structrurized data
     *
     * @param mixed     Data
     */
    public function output($data = null)
    {
        $this->provider['structurized_data'] = $data;
    }

    /**
     * Set HTTP status code
     *
     * @param int       Code
     */
    public function httpStatus($code = 200)
    {
        $this->provider['http_status_code'] = \intval($code);
    }

    /**
     * Set route name
     *
     * @param string    Name
     */
    public function setName($name)
    {
        return;
    }

    /**
     * Generate HTTP SDK
     */
    public function generateSdk()
    {
        return;
    }

    /**
     * Generate API document
     */
    public function generateDoc()
    {
        return;
    }

    /**
     * @brief Set middlewares
     *
     * @return boolean  Status
     */
    private function _setMiddleWares()
    {
        $configs = [];
        $sm = $this->supported_middlewares;
        \uksort($this->middlewares, function ($key1, $key2) use ($sm) {
            $v1 = $sm[$key1];
            $v2 = $sm[$key2];

            return ($v1 > $v2) ? 1 : (($v1 == $v2) ? 0 : -1);
        });
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

    /**
     * Magic method for framework penetrate
     */
    public function __call($method, $arguments)
    {
        // Try call method to framework instance
        if (\method_exists($this->app, $method))
        {
            \call_user_func_array([$this->app, $method], $arguments);
        }

        return $this;
    }
}

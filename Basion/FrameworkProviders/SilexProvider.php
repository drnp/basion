<?php
/**
 * Basion RESTful toolkit
 *
 * @link        http://bsgroup.org/basion
 * @author      Dr.NP <np@bsgroup.org>
 */

namespace Basion\FrameworkProviders;

use Basion\Interfaces\FrameworkProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Silex provider
 *
 * @package     Basion
 * @since       1.0.0
 */
class SilexProvider extends ProviderBase implements FrameworkProviderInterface
{
    /**
     * Framework path
     *
     * @var string
     */
    private $framework_path;

    /**
     * Framework instance
     *
     * @var object;
     */
    private $app            = null;

    // Construct
    function __construct($path)
    {
        $this->framework_path = \trim($path) . '/vendor/autoload.php';
    }

    /**
     * Init a new silex application
     *
     * @return Application instance
     */
    public function initFramework()
    {
        require $this->framework_path;
        $app = new \Silex\Application();
        $this->app = $app;

        return $app;
    }

    /**
     * Start application
     */
    public function startFramework()
    {
        $this->app->run();
    }

    /**
     * Get framework instance
     *
     * @return object
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Abstract method: setBeforeMiddleWare
     */
    public function setBeforeMiddleWare(callable $f)
    {
        $self = &$this;
        $this->app->before(function () use ($f, $self) {
            \call_user_func($f, $self);
        });

        return;
    }

    /**
     * Abstract method: setAfterMiddleWare
     */
    public function setAfterMiddleWare(callable $f)
    {
        $self = &$this;
        $this->app->after(function (Request $request, Response $response) use ($f, $self) {
            $self['http_status_code'] = $response->getStatusCode();
            \call_user_func($f, $self);
            if ($self['headers'] && \is_array($self['headers']))
            {
                foreach ($self['headers'] as $key => $value)
                {
                    $response->headers->set($key, $value);
                }

                unset($self['headers']);
            }

            if ($self['raw_data'])
            {
                $response->setContent($self['raw_data']);
                unset($self['raw_data']);
            }
        });

        return;
    }

    /**
     * Abstract method: setAbortMiddleWare
     */
    public function setAbortMiddleWare(callable $f)
    {
        // Silex does not support abort middleware any more
        return;
    }

    /**
     * Abstact method: setFinishMiddleWare
     */
    public function setFinishMiddleWare(callable $f)
    {
        $self = &$this;
        $this->app->finish(function (Request $request, Response $response) use ($f, $self) {
            \call_user_func($f, $self);
        });

        return;
    }
}

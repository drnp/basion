<?php
/**
 * Basion RESTful toolkit
 *
 * @link        http://bsgroup.org/basion
 * @author      Dr.NP <np@bsgroup.org>
 */

namespace Basion\FrameworkProviders;

/**
 * Silex provider
 *
 * @package     Basion
 * @since       1.0.0
 */
class SilexProvider implements FrameworkProviderInterface
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
    function __construc($path)
    {
        $this->framework_path = \trim($path);
    }

    /**
     * Start a new silex application
     *
     * @return 
     */
    public function initFramework()
    {
        require_once($this->framework_path);
        $app = new Silex\Application();

        return $app;
    }

    /**
     * Get framework instance
     *
     * @return object
     */
    public function getApp()
    {
        return $app;
    }
}

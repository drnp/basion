<?php
/**
 * Basion RESTful toolkit
 *
 * @link        http://bsgroup.org/basion
 * @author      Dr.NP <np@bsgroup.org>
 */

namespace Basion\MiddleWares;

use Basion\Interfaces\MiddleWareInterface;

/**
 * Restful API version via user-definded header
 *
 * @package basion
 * @since 1.0.0
 */
class ApiVersion implements MiddleWareInterface
{
    /**
     * Abstract method: before
     */
    public static function before()
    {
        $provider = \func_get_arg(0);
        $config = $provider['middlewares']['config']['ApiVersion'];
        $header_item = (isset($config['header'])) ? \trim($config['header']) : 'Api-Version';
        $key = 'HTTP_' . \str_replace('-', '_', \strtoupper($header_item));
        $version = \filter_input(\INPUT_SERVER, $key);
        $provider['api_version'] = intval($version);
    }

    /**
     * Abstract method: after
     */
    public static function after()
    {

    }

    /**
     * Abstract method: abort
     */
    public static function abort()
    {

    }

    /**
     * Abstract method: finish
     */
    public static function finish()
    {

    }
}

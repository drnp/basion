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
 * Authorization token via HTTP header
 *
 * @package basion
 * @since 0.0.1
 */
class Authorization implements MiddleWareInterface
{
    /**
     * Abstract method: before
     */
    public static function before()
    {
        $provider = \func_get_arg(0);
        $config = $provider['middlewares']['config']['Authorization'];
        $raw = \filter_input(\INPUT_SERVER, 'HTTP_AUTHORIZATION');
        $scheme = (isset($config['scheme']) && \is_string($config['scheme'])) ? $config['scheme'] : 'basion';
        $scheme = \strtoupper($scheme);
        $token = '';
        if ($raw)
        {
            $partten = '|^' . $scheme . '\s+(\w+)|i';
            if (\preg_match($partten, $raw, $matches) > 0)
            {
                $token = $matches[1];
            }
        }

        $provider['authorization_token'] = $token;
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

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
 * HTTP Content-type && Accept assemble
 *
 * @package     Basion
 * @since       1.0.0
 */
class ContentType implements MiddleWareInterface
{
    /**
     * Supported content type
     *
     * @var array
     */
    private static $supported_content_type = [
        'text/plain' => 'normal', 
        'text/html' => 'normal', 
        'text/xml' => 'xml', 
        'application/xml' => 'xml', 
        'application/json' => 'json', 
        'application/x-php' => 'php'
    ];

    /**
     * Current content type
     *
     * @var string
     */
    private static $content_type    = 'text/html';

    /**
     * Encode method
     *
     * @var string
     */
    private static $encode_method   = 'normal';

    /**
     * Abstract method: before
     */
    public static function before()
    {
        $provider = \func_get_arg(0);
        $accept = \filter_input(\INPUT_SERVER, 'HTTP_ACCEPT');
        $acceptable = \explode(',', $accept);
        $accept_content_types = [];
        foreach ($acceptable as $item)
        {
            $q = 1;
            $tmp = \explode(';q=', $item);
            $type = $tmp[0];
            if (isset($tmp[1]))
            {
                $q = \floatval($tmp[1]);
            }

            $accept_content_types[\strtolower(\trim($type))] = \floatval($q);
        }

        \arsort($accept_content_types, \SORT_NUMERIC);
        foreach ($accept_content_types as $type => $q)
        {
            if (isset(self::$supported_content_type[$type]))
            {
                self::$content_type = $type;
                self::$encode_method = self::$supported_content_type[$type];
                break;
            }
        }
    }

    /**
     * Abstract method: after
     */
    public static function after()
    {
        $provider = \func_get_arg(0);
        $output = '';
        switch (self::$encode_method)
        {
            case 'xml' : 
                break;
            case 'json' : 
                $output = \json_encode($provider['structurized_data'], \JSON_NUMERIC_CHECK | \JSON_PRETTY_PRINT);
                break;
            case 'normal' : 
            case 'php' : 
            default : 
                $output = \serialize($provider['structurized_data']);
                break;
        }

        $provider['raw_data'] = $output;
        $provider['headers'] = ['Content-Type' => self::$content_type];
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

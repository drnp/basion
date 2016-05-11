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
    private static $supported_accept = [
        'text/plain' => 'normal', 
        'text/html' => 'normal', 
        'text/xml' => 'xml', 
        'application/xml' => 'xml', 
        'application/json' => 'json', 
        'application/x-yaml' => 'yaml'
    ];

    private static $supported_content_type = [
        'application/x-www-form-urlencoded' => 'php', 
        'multipart/form-data' => 'php', 
        'text/xml' => 'xml', 
        'application/xml' => 'xml', 
        'application/json' => 'json', 
        'application/x-yaml' => 'yaml'
    ];

    /**
     * Accept type
     *
     * @var string
     */
    private static $accept          = 'text/html';
    /**
     * Current content type
     *
     * @var string
     */
    private static $content_type    = 'application/x-www-form-urlencoded';

    /**
     * Encode method
     *
     * @var string
     */
    private static $encode_method   = 'normal';

    /**
     * Decode method
     *
     * @var string
     */
    private static $decode_method   = 'php';

    /**
     * Abstract method: before
     */
    public static function before()
    {
        $provider = \func_get_arg(0);

        // HTTP accept
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
            if (isset(self::$supported_accept[$type]))
            {
                self::$accept = $type;
                self::$encode_method = self::$supported_accept[$type];
                break;
            }
        }

        // HTTP Content-type (POST | PUT)
        $content_type = \strtolower(\trim(\filter_input(\INPUT_SERVER, 'HTTP_CONTENT_TYPE')));
        if (isset(self::$supported_content_type[$content_type]))
        {
            self::$content_type = $content_type;
            self::$decode_method = self::$supported_content_type[$content_type];
        }

        // Input data
        $input = $_GET;
        if ('post' == $provider['http_request_method'])
        {
            $post_data = [];
            $raw = \file_get_contents('php://input');
            switch (self::$decode_method)
            {
                case 'php' : 
                    $post_data = $_POST;
                    break;
                case 'xml' : 
                    $service = new \Sabre\Xml\Service();
                    $post_data = $service->parse($raw);
                    break;
                case 'json' : 
                    $post_data = \json_decode($raw, true);
                    break;
                case 'yaml' : 
                    $post_data = \Symfony\Component\Yaml\Yaml::parse($raw);
                    break;
                default : 
                    break;
            }

            $input = \array_merge($input, $post_data);
        }

        $provider['input_data'] = $input;
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
                $service = new \Sabre\Xml\Service();
                $output = $service->write($provider['structurized_data']);
                break;
            case 'json' : 
                $output = \json_encode($provider['structurized_data'], \JSON_NUMERIC_CHECK | \JSON_PRETTY_PRINT);
                break;
            case 'yaml' : 
                $output = \Symfony\Component\Yaml\Yaml::dump($provider['structurized_data']);
                break;
            case 'normal' : 
            case 'php' : 
            default : 
                $output = \serialize($provider['structurized_data']);
                break;
        }

        $provider['raw_data'] = $output;
        $provider['headers'] = ['Content-Type' => self::$accept];
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

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
 * API result data envelope
 *
 * @package basion
 * @since 1.0.0
 */
class DataEnvelope implements MiddleWareInterface
{
    /**
     * Abstract method: before
     */
    public static function before()
    {
        $provider = \func_get_arg(0);
        $config = $provider['middlewares']['config']['DataEnvelope'];
        $provider['need_envelope'] = true;
        if (isset($config['force_header']) && \is_string($config['force_header']))
        {
            $force_value = \filter_input(\INPUT_SERVER, 'HTTP_' . \trim(\str_replace('-', '_', \strtoupper($config['force_header']))));
            $provider['need_envelope'] = $force_value ? true : false;
        }
    }

    /**
     * Abstract method: after
     */
    public static function after()
    {
        $provider = \func_get_arg(0);
        $config = $provider['middlewares']['config']['DataEnvelope'];
        if ($provider['need_envelope'])
        {
            $envelope = [];
            if (isset($config['http_status_code_field']) && \is_string($config['http_status_code_field']))
            {
                $envelope[$config['http_status_code_field']] = \intval($provider['http_status_code']);
            }

            if (isset($config['error_code_field']) && \is_string($config['error_code_field']))
            {
                $envelope[$config['error_code_field']] = \intval($provider['error_code']);
            }

            if (isset($config['error_message_field']) && \is_string($config['error_message_field']))
            {
                $envelope[$config['error_message_field']] = \trim($provider['error_message']);
            }

            $data_field = (isset($config['data_field'])) ? \trim($config['data_field']) : 'data';
            $envelope[$data_field] = $provider['structurized_data'];

            // Override data
            $provider['structurized_data'] = $envelope;
        }
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

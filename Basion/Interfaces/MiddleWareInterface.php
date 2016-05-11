<?php
/**
 * Basion RESTful toolkit
 *
 * @link        http://bsgroup.org/basion
 * @author      Dr.NP <np@bsgroup.org>
 */

namespace Basion\Interfaces;

/**
 * Interface of middle ware
 *
 * @package     Basion
 * @since       1.0.0
 */
interface MiddleWareInterface
{
    /**
     * Before
     */
    public static function before();

    /**
     * After
     */
    public static function after();

    /**
     * Abort
     */
    public static function abort();

    /**
     * Finish
     */
    public static function finish();
}

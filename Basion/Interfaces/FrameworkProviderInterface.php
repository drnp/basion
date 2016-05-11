<?php
/**
 * Basion RESTful toolkit
 *
 * @link        http://bsgroup.org/basion
 * @author      Dr.NP <np@bsgroup.org>
 */

namespace Basion\Interfaces;

/**
 * Interface of framework provider
 *
 * @package     Basion
 * @since       1.0.0
 */
interface FrameworkProviderInterface
{
    /**
     * Initial original framework
     */
    public function initFramework();

    /**
     * Start original framework
     */
    public function startFramework();

    /**
     * Get original instance of framework
     *
     * @return object   Application
     */
    public function getApp();

    /**
     * Set <before> middleware
     */
    public function setBeforeMiddleWare(callable $f);

    /**
     * Set <after> middleware
     */
    public function setAfterMiddleWare(callable $f);

    /**
     * Set <abort> middleware
     */
    public function setAbortMiddleWare(callable $f);

    /**
     * Set <finish> middleware
     */
    public function setFinishMiddleWare(callable $f);
}

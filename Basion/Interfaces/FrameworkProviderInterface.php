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
     * Set structurized context data
     *
     * @param mixed     Data
     *
     * @return boolean  Result
     */
    public function setStructurizedData($data);

    /**
     * Get structurized context data
     *
     * @return mixed    Data
     */
    public function getStructurizedData();
}

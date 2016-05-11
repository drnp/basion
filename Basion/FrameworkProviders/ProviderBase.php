<?php
/**
 * Basion RESTful toolkit
 *
 * @link        http://bsgroup.org/basion
 * @author      Dr.NP <np@bsgroup.org>
 */

namespace Basion\FrameworkProviders;

/**
 * Base provider class
 *
 * @package     Basion
 * @since       1.0.0
 */
class ProviderBase implements \ArrayAccess
{
    /**
     * Container contents
     *
     * @var array
     */
    private $container    = [];

    /**
     * Abstract method of ArrayAccess::offsetSet
     *
     * @param string | null     Offset
     * @param mixed     Data
     */
    public function offsetSet($offset, $value)
    {
        if (\is_null($offset))
        {
            $this->container[] = $value;
        }
        else
        {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Abstract method of ArrayAccess::offsetGet
     *
     * @param string    Offset
     *
     * @return mixed    Data
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Abstract method of ArrayAccess::offsetExists
     *
     * @param string    Offset
     *
     * @return boolean  Result
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Abstract method of ArrayAccess::offsetUnset
     *
     * @param string    Offset
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }
}

<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\AbstractCollection;

/**
 * @method Method current()
 * @method Method offsetGet($index)
 */
class Methods extends AbstractCollection
{
    /**
     * Determines of the value is a valid collection value
     *
     * @param  mixed $value
     *
     * @return boolean
     */
    protected function isValidValue($value)
    {
        return ($value instanceof Method);
    }
}

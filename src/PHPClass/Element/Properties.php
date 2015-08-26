<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\AbstractCollection;

/**
 * @method Property current()
 * @method Property offsetGet($index)
 */
class Properties extends AbstractCollection
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
        return ($value instanceof Property);
    }
}

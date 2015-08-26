<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\AbstractCollection;

/**
 * @method ClassConstant current()
 * @method ClassConstant offsetGet($index)
 */
class ClassConstants extends AbstractCollection
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
        return ($value instanceof ClassConstant);
    }
}

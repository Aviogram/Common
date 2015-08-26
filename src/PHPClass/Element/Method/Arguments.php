<?php
namespace Aviogram\Common\PHPClass\Element\Method;

use Aviogram\Common\AbstractCollection;

/**
 * @method Argument current()
 * @method Argument offsetGet($index)
 */
class Arguments extends AbstractCollection
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
        return ($value instanceof Argument);
    }
}

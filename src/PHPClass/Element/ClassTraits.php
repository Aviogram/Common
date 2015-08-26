<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\AbstractCollection;
use Aviogram\Common\PHPClass\Exception;

/**
 * @method ClassTrait current()
 * @method ClassTrait offsetGet($index)
 */
class ClassTraits extends AbstractCollection
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
        return ($value instanceof ClassTrait);
    }
}

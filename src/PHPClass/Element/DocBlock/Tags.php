<?php
namespace Aviogram\Common\PHPClass\Element\DocBlock;

use Aviogram\Common\AbstractCollection;

/**
 * @method Tag current()
 * @method Tag offsetGet($index)
 */
class Tags extends AbstractCollection
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
        return ($value instanceof Tag);
    }
}

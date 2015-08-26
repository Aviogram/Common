<?php
namespace Aviogram\Common\PHPClass\Exception;

class Type extends BaseException
{
    /**
     * @param string $type
     *
     * @return ClassFile
     */
    public static function isNotDefined($type)
    {
        return new self("Type '{$type}' is not defined.");
    }
}

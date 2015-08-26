<?php
namespace Aviogram\Common\PHPClass\Exception;

class ClassUse extends BaseException
{

    /**
     * @param  string $class
     *
     * @return ClassUse
     */
    public static function classUseNotFound($class)
    {
        return new self("Class '{$class}' for use statement could not be found.");
    }
}

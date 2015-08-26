<?php
namespace Aviogram\Common\Exception;

class Collection extends BaseException
{
    /**
     * @param string $value
     * @param string $class
     *
     * @return Collection
     */
    public static function invalidType($value, $class)
    {
        $type = is_object($value) ? get_class($value) : var_export($value);

        return new self("Could not add '{$type}' to collection '{$class}'.");
    }

    /**
     * @param  string $class
     *
     * @return Collection
     */
    public static function readOnly($class)
    {
        return new self("Collection '{$class}' has been marked as read-only. No modifications can be made.");
    }
}

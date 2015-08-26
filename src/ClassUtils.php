<?php
namespace Aviogram\Common;

class ClassUtils
{
    /**
     * @param  string $class
     *
     * @return bool
     */
    public static function classExists($class)
    {
        return class_exists($class);
    }

    /**
     * @param  string $interface
     *
     * @return bool
     */
    public static function interfaceExists($interface)
    {
        return interface_exists($interface);
    }

    /**
     * @param  string $trait
     *
     * @return bool
     */
    public static function traitExists($trait)
    {
        return trait_exists($trait);
    }

    /**
     * Get the namespace of the class
     *
     * @param  string $class
     *
     * @return string|null
     */
    public static function classNamespace($class)
    {
        $parts = static::parseClass($class);

        return $parts[0];
    }

    /**
     * Get the className segment of the given class
     *
     * @param  string $class
     *
     * @return string
     */
    public static function className($class)
    {
        $parts = static::parseClass($class);

        return $parts[1];
    }

    /**
     * Parse the class to a namespace and className part
     *
     * @param  string $class
     *
     * @return array    array(<namespace>, <className>)
     */
    protected static function parseClass($class)
    {
        static $cache = array();

        if (array_key_exists($class, $cache) === false) {
            // Make reference for original input
            $string = $class;

            if ($string[0] === '\\') {
                $string = substr($string, 1);
            }

            if (preg_match('/^(?<namespace>.*)\\\\(?<className>.+?)$/', $string, $matches)) {
                $cache[$class] = array($matches['namespace'], $matches['className']);
            } else {
                $cache[$class] = array(null, $class);
            }
        }

        return $cache[$class];
    }
}

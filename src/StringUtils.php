<?php
namespace Aviogram\Common;

class StringUtils
{
    /**
     * Convert a underscore defined string to his PascalCase format
     *
     * @param  string $string
     *
     * @return string
     */
    public static function underscoreToPascalCase($string)
    {
        return static::underscoreToCamelCase("_{$string}");
    }

    /**
     * Convert a underscore defined string to his camelCase format
     *
     * @param  string $string
     *
     * @return string
     */
    public static function underscoreToCamelCase($string)
    {
        static $cache = array();

        if (array_key_exists($string, $cache) == true) {
            return $cache[$string];
        }

        $replacer = function(array $matches) {
            return strtoupper($matches[1]);
        };

        return $cache[$string] = preg_replace_callback('/_(.?)/', $replacer, $string);
    }

    /**
     * Convert a PascalCased defined string to his underscore format
     *
     * @param  string $string
     *
     * @return string
     */
    public static function pascalCaseToUnderscore($string)
    {
        return static::camelCaseToUnderscore(lcfirst($string));
    }

    /**
     * Convert a camelCased defined string to his underscore format
     *
     * @param  string $string
     *
     * @return string
     */
    public static function camelCaseToUnderscore($string)
    {
        static $cache = array();

        if (array_key_exists($string, $cache) === true) {
            return $cache[$string];
        }

        $replacer = function(array $matches) {
            return '_' . strtolower($matches[0]);
        };

        return $cache[$string] = preg_replace_callback('/[A-Z]/', $replacer, $string);
    }
}

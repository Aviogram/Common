<?php
namespace Aviogram\Common;

class ArrayUtils
{
    /**
     * Get an value from an array via a dotted notation
     *
     * @param  string $needle       (my.scope.notation) Use \. to escape . characters
     * @param  array  $haystack
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function targetGet($needle, array $haystack, $default = null)
    {
        if ($needle === null) {
            return $haystack;
        }

        $parts  = preg_split('/(?<!\\\\)\./', $needle);

        foreach ($parts as $part) {
            $key = str_replace('\\.', '.', $part);

            if (
                is_array($haystack) === false ||
                array_key_exists($key, $haystack) === false
            ) {
                return $default;
            }

            $haystack = $haystack[$part];
        }

        return $haystack;
    }

    /**
     * Set an value on a multi dimensional array via a dotted string notation
     *
     * @param  string $needle
     * @param  string $value
     * @param  array  $haystack
     *
     * @return array
     */
    public static function targetSet($needle, $value, array $haystack = array())
    {
        $keys = explode('.', $needle);
        $loop = &$haystack;

        foreach ($keys as $key) {
            if (is_array($loop) === false) {
                $loop = array();
            }

            if (array_key_exists($key, $loop) === false) {
                $loop[$key] = array();
            }

            $loop = &$loop[$key];
        }

        $loop = $value;

        return $haystack;
    }
}

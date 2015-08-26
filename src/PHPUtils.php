<?php
namespace Aviogram\Common;

class PHPUtils
{
    /**
     * Return a list of reserved keywords
     *
     * @return string[]
     */
    public static function reservedKeywords()
    {
        return array(
            '__halt_compiler',
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'callable',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'die',
            'do',
            'echo',
            'else',
            'elseif',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'eval',
            'exit',
            'extends',
            'final',
            'finally',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'isset',
            'list',
            'namespace',
            'new',
            'or',
            'print',
            'private',
            'protected',
            'public',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'unset',
            'use',
            'var',
            'while',
            'xor',
            'yield'
        );
    }

    /**
     * Return a list of reserved constant names
     *
     * @return string[]
     */
    public static function reservedConstants()
    {
        return array(
            '__CLASS__',
            '__NAMESPACE__',
            '__TRAIT__',
            '__DIR__',
            '__FILE__',
            '__FUNCTION__',
            '__METHOD__',
            '__LINE__'
        );
    }

    /**
     * Checks if the name is a reserved keyword
     *
     * @param string $name
     *
     * @return boolean
     */
    public static function isReservedKeyword($name)
    {
        return in_array(strtolower($name), static::reservedKeywords());
    }

    /**
     * Checks if the name is a reserved constant
     *
     * @param string $name
     *
     * @return boolean
     */
    public static function isReservedConstant($name)
    {
        return in_array(strtoupper($name), static::reservedConstants());
    }

    /**
     * Checks if the name is a reserved keyword or constant
     *
     * @param  string $name
     *
     * @return bool
     */
    public static function isReservedAny($name)
    {
        return static::isReservedKeyword($name) || static::isReservedConstant($name);
    }
}

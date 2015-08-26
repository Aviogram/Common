<?php
namespace Aviogram\Common\PHPClass\Exception;

class ClassFile extends BaseException
{
    /**
     * @param  string $type
     *
     * @return ClassFile
     */
    public static function classTypeDoesNotExists($type)
    {
        return new self("Class type '{$type}' does not exists.");
    }

    /**
     * @param  string $interface
     *
     * @return ClassFile
     */
    public static function interfaceNotFound($interface)
    {
        return new self("Interface '{$interface}' could not be found.");
    }

    /**
     * @param  string $class
     *
     * @return ClassFile
     */
    public static function classNotFound($class)
    {
        return new self("Class '{$class}' could not be found.");
    }

    /**
     * @param  string $trait
     *
     * @return ClassFile
     */
    public static function traitNotFound($trait)
    {
        return new self("Trait '{$trait}' could not be found.");
    }

    /**
     * @param string $class
     * @param string $method
     *
     * @return ClassFile
     */
    public static function methodAlreadyDefined($class, $method)
    {
        return new self("{$class}::{$method}() is already defined and cannot be created again.");
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return ClassFile
     */
    public static function invalidPropertyName($class, $property)
    {
        return new self("{$class}::\${$property} is an invalid property name.");
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return ClassFile
     */
    public static function propertyAlreadyDefined($class, $property)
    {
        return new self("{$class}::\${$property} is already defined and cannot be created again.");
    }

    /**
     * @param  string $className
     *
     * @return ClassFile
     */
    public static function invalidClassName($className)
    {
        return new self("Class name '{$className}' is invalid, use a none reserved keyword/constant as name.");
    }

    /**
     * @param  string $className
     * @param  string $methodName
     *
     * @return ClassFile
     */
    public static function invalidMethodName($className, $methodName)
    {
        return new self("{$className}::{$methodName}() is a invalid name, use a none reserved keyword/constant as name.");
    }

    /**
     * @param  string $className
     * @param  string $constantName
     *
     * @return ClassFile
     */
    public static function constantAlreadyDefined($className, $constantName)
    {
        return new self("{$className}::{$constantName} is a invalid name, use a none reserved keyword/constant as name.");
    }

    /**
     * @param  string $className
     * @param  string $constantName
     *
     * @return ClassFile
     */
    public static function invalidConstantName($className, $constantName)
    {
        return new self("{$className}::{$constantName} is a invalid name, use a none reserved keyword/constant as name.");
    }
}

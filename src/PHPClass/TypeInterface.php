<?php
namespace Aviogram\Common\PHPClass;

interface TypeInterface
{
    const PHP_TYPE_STRING   = 'string';
    const PHP_TYPE_INTEGER  = 'integer';
    const PHP_TYPE_BOOLEAN  = 'boolean';
    const PHP_TYPE_FLOAT    = 'float';
    const PHP_TYPE_OBJECT   = 'object';
    const PHP_TYPE_MIXED    = 'mixed';
    const PHP_TYPE_ARRAY    = 'array';
    const PHP_TYPE_RESOURCE = 'resource';
    const PHP_TYPE_VOID     = 'void';
    const PHP_TYPE_NULL     = 'null';
    const PHP_TYPE_CALLABLE = 'callable';
    const PHP_TYPE_SELF     = 'self';
}

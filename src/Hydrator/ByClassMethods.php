<?php
namespace Aviogram\Common\Hydrator;

use ArrayIterator;
use Aviogram\Common\StringUtils;
use ReflectionClass;
use ReflectionMethod;

class ByClassMethods
{
    /**
     * Runtime caching for performance
     *
     * @var array
     */
    protected $classMethodsCache = array();

    /**
     *  A list of exceptions that can be thrown by the hydrator
     *
     * @var array
     */
    protected $exceptions = array(
        1 => 'First argument of ClassMethods::hydrate() should be an object, %s given.',
        2 => 'Second argument of ClassMethods::hydrate() should be an array or implements ArrayIterator, %s given.',
        3 => '%s::%s expects an %s, %s given',
    );

    /**
     * Hydrate aan array to an object. By default in recursive manner
     *
     * @param object $object
     * @param array | ArrayIterator $data
     * @param boolean $recursive
     * @param array $remainder Contains the unhydrated values from data not
     * added to the target object
     *
     * @return object
     * @throws Exception\HydrateFailed When $object is no object or $data is not array
     */
    public function hydrate(
        $object,
        $data,
        $recursive = true,
        &$remainder = array()
    ) {
        // Object should be an object. Duh makes sense ;)
        if (is_object($object) === false) {
            $type = gettype($object);
            throw new Exception\HydrateFailed(sprintf($this->exceptions[1], $type), 1);
        }

        // The input data should be an array or should implements ArrayIterator
        if (
            is_array($data) === false &&
            ($data instanceof ArrayIterator) === false
        ) {
            $type = gettype($data);
            throw new Exception\HydrateFailed(sprintf($this->exceptions[2], $type), 2);
        }

        // Fetch all the set methods with method information
        $methods = $this->getSetters($object);

        // Is ArrayIterator
        $isIterator   = (bool) ($object instanceof ArrayIterator);

        // Loop through the input data
        foreach ($data as $offset => $value) {
            // Generate setMethod name
            $setMethod = StringUtils::underscoreToCamelCase('set_' . $offset);

            // If method does not exists on the object, continue
            if ($methods->offsetExists($setMethod) === false) {
                $setMethod = 'setOffsetEntity';

                if ($methods->offsetExists($setMethod) === false) {
                    // When the method has implemented an iterator. We can
                    // set the offset for injection the data
                    if ($isIterator === true) {
                        $object->offsetSet($offset, $value);
                    }
                    $remainder[$offset] = $value;
                    continue;
                }
            }

            // Get method information
            $method = $methods->offsetGet($setMethod);

            // Validate input against typehint array
            if ($method->isTypehintArray() && ($method->isRequired() === true && is_array($value) === false)) {
                $class = get_class($object);
                $type  = is_object($value) ? get_class($value) : gettype($value);

                throw new Exception\HydrateFailed(
                    sprintf($this->exceptions[3], $class, $setMethod, 'array', $type),
                    3
                );
                // Validate input against typehint object
            } else if (
                $method->isTypehintArray() === false &&
                $method->getTypehint() !== null
            ) {
                $typehint = $method->getTypehint();

                // Hydrate to an object when the input data is an array
                if ($recursive === true && is_object($value) === false && is_array($value)) {
                    $value = $this->hydrate(new $typehint, $value);
                }

                // If the parameter is required. It should have instanceof check
                if ($method->isRequired() && ($value instanceof $typehint) === false) {
                    $class = get_class($object);
                    $type  = is_object($value) ? get_class($value) : gettype($value);

                    throw new Exception\HydrateFailed(
                        sprintf($this->exceptions[3], $class, $setMethod, $typehint, $type),
                        3
                    );
                }
            }

            // Set the actual value
            $object->{$setMethod}($value, $offset);
        }

        return $object;
    }

    /**
     * Get the setters of an object
     *
     * @param mixed $object
     * @return Collection\Method
     */
    protected function getSetters($object)
    {
        // Get the current class
        $class = get_class($object);

        // Check if we have already parsed this class
        if (array_key_exists($class, $this->classMethodsCache) === true) {
            return $this->classMethodsCache[$class];
        }

        // Regex for parsing ReflectionParameter::__toString()
        $regex     = '/\[\s\<(?P<required>[^\>]+)\>\s' .
            '(?:(?P<typehint_a>[^\s]+|(?P<typehint_b>[^\s]+)\sor\s(?P<typehint_c>[^\s]+))\s)?' .
            '(?P<variable>\$[^\s]+)\s(?:=\s(?P<default>[^\]]+)\s)?\]/';

        // Make reflection and fetch the methods
        $reflectionClass = new ReflectionClass($object);
        $methods         = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        // Make result container
        $setters         = new Collection\Method();

        // Loop through the methods
        foreach ($methods as $method) {
            // We only care about the setters
            if (substr($method->getName(), 0, 3) !== 'set') {
                continue;
            }

            // Get parameter collection
            $parameters = $method->getParameters();

            // We only take care of the first parameter of the setter
            $parameter  = $parameters[0];
            $raw        = (string) $parameter;

            // If we cannot match it we continue
            if (($result = preg_match($regex, $raw, $matches)) === false || $result === 0) {
                continue;
            }

            // Parse the result of the regex
            $required = ($matches['required'] === 'required');
            $variable = $matches['variable'];
            $default  = $required ? null : $matches['default'];
            $typehint = null;

            // Advanced typehint detection
            if (empty($matches['typehint_b']) === false) {
                $typehint = $matches['typehint_b'];
            } else if (empty($matches['typehint_a']) === false){
                $typehint = $matches['typehint_a'];
            }

            // Set the correct default value
            switch ($default) {
                case null:
                case 'NULL':
                    $default = null;
                    break;
                case 'Array':
                    $default = array();
                    break;
                default:
                    $default = trim($default, "'");
                    break;
            }

            // Has it child objects
            $childObject = ($typehint !== null && $typehint !== 'array');

            // Set the results the the response object
            $setters->offsetSet(
                $method->getName(),
                new Entity\Method(
                    $required,
                    $default,
                    $typehint,
                    $childObject
                )
            );
        }

        // Return the just generated response
        return $this->classMethodsCache[$class] = $setters;
    }
}

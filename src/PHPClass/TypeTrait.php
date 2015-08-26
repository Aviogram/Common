<?php
namespace Aviogram\Common\PHPClass;

trait TypeTrait
{
    /**
     * Other notations of a type. Covert them to standard value
     *
     * @var array
     */
    private $phpTypeMapping = array(
        'int'    => 'integer',
        'bool'   => 'boolean',
        'double' => 'float'
    );

    /**
     * Known types
     *
     * @var array
     */
    private $phpTypes = array(
        'string',
        'integer',
        'boolean',
        'float',
        'object',
        'mixed',
        'array',
        'resource',
        'void',
        'null',
        'callable',
        'self',
    );

    /**
     *  Checks if the class is defined or not
     *
     * @param  string $class
     *
     * @return boolean
     */
    abstract protected function isClassDefined($class);

    /**
     *  Checks if the interface is defined or not
     *
     * @param  string $interface
     *
     * @return boolean
     */
    abstract protected function isInterfaceDefined($interface);

    /**
     *  Checks if the trait is defined or not
     *
     * @param  string $trait
     *
     * @return boolean
     */
    abstract protected function isTraitDefined($trait);

    /**
     * Get and validates the type given
     *
     * @param string $input Any format int[], boolean, null|string etc
     *
     * @return string
     * @throws Exception\ClassFile  When the type/class cannot be found
     */
    public function getType($input)
    {
        // Make an array to loop through
        $inputs = explode('|', $input);

        foreach ($inputs as $index => $input) {
            $list = (bool) preg_match('/\[\]$/', $input);
            if ($list === true) {
                $input = substr($input, 0, -2);
            }

            // make the type case insensitive
            $type = strtolower(trim($input));

            // Check if we can convert the type to a known type
            if (array_key_exists($type, $this->phpTypeMapping) === true) {
                $type = $this->phpTypeMapping[$type];
            }

            // Check if it is a predefined type
            if (in_array($type, $this->phpTypes) === true) {
                $inputs[$index] = $type . ($list ? '[]' : '');
                continue;
            }

            // Reset the type. Maybe it is a class definition
            $type = $input;

            if (
                $this->isClassDefined($type) === true ||
                $this->isInterfaceDefined($type) === true ||
                $this->isTraitDefined($type) === true
            ) {
                $inputs[$index] = $type . ($list ? '[]' : '');
                continue;
            }

            throw Exception\Type::isNotDefined($type);
        }

        return implode('|', $inputs);
    }
}

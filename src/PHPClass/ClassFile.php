<?php
namespace Aviogram\Common\PHPClass;

use Aviogram\Common\ClassUtils;
use Aviogram\Common\PHPUtils;

class ClassFile implements ElementInterface, ScopeInterface, TypeInterface
{
    use TypeTrait;

    const CLASS_TYPE_CLASS     = 'class';
    const CLASS_TYPE_INTERFACE = 'interface';
    const CLASS_TYPE_TRAIT     = 'trait';

    /**
     * Available class types
     *
     * @var array
     */
    protected $classTypes = array(
        self::CLASS_TYPE_CLASS,
        self::CLASS_TYPE_INTERFACE,
        self::CLASS_TYPE_TRAIT
    );

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var Element\ClassUses
     */
    protected $uses;

    /**
     * @var Element\Methods
     */
    protected $methods;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $classType;

    /**
     * @var Element\DocBlock | null
     */
    protected $classDocBlock;

    /**
     * @var bool
     */
    protected $abstractClass = false;

    /**
     * @var string
     */
    protected $extends;

    /**
     * @var string
     */
    protected $implements;

    /**
     * @var Element\ClassTraits
     */
    protected $classTraits;

    /**
     * @var Element\Properties
     */
    protected $properties;

    /**
     * @var Element\ClassConstants
     */
    protected $classConstants;

    /**
     * Initialize ClassFile
     *
     * @param string $class     The full class you want to create
     * @param string $classType See ClassFile::CLASS_TYPE_*
     * @param bool   $abstract  If the class is abstract
     *
     * @throws Exception\ClassFile
     */
    public function __construct($class, $classType = self::CLASS_TYPE_CLASS, $abstract = false)
    {
        $this->uses           = new Element\ClassUses($this);
        $this->methods        = new Element\Methods();
        $this->classTraits    = new Element\ClassTraits();
        $this->properties     = new Element\Properties();
        $this->classConstants = new Element\ClassConstants();

        $this->className = ClassUtils::className($class);
        $this->namespace = ClassUtils::classNamespace($class);

        if (PHPUtils::isReservedAny($this->className) === true) {
            throw Exception\ClassFile::invalidClassName($this->className);
        }

        if (in_array($classType, $this->classTypes) === false) {
            throw Exception\ClassFile::classTypeDoesNotExists($classType);
        }

        $this->classType     = $classType;
        $this->abstractClass = $abstract;
    }

    /**
     * @return Element\DocBlock
     */
    public function getClassDocBlock()
    {
        return $this->classDocBlock ?: $this->classDocBlock = new Element\DocBlock($this);
    }

    /**
     * Add a class/interface where we going to extend from
     * Add \ if you want to specify a full class otherwise the system will use
     *
     * @param  string $class
     *
     * @param bool    $importClass  When TRUE we add the class/interface to the uses when it's starts with \
     *
     * @return $this
     * @throws Exception\ClassFile
     */
    public function extendClass($class, $importClass = true)
    {
        switch ($this->classType) {
            case static::CLASS_TYPE_INTERFACE:
                if ($this->isInterfaceDefined($class) === false) {
                    throw Exception\ClassFile::interfaceNotFound($class);
                }
                break;
            default:
                if ($this->isClassDefined($class) === false) {
                    throw Exception\ClassFile::classNotFound($class);
                }
                break;
        }

        if ($importClass === true && $class[0] === '\\') {
            $this->uses->addUse(substr($class, 1));
            $class = substr($class, strrpos($class, '\\') + 1);
        }

        $this->extends[] = $class;

        return $this;
    }

    /**
     * Add interface
     *
     * @param  string $interface
     *
     * @return $this
     * @throws Exception\ClassFile
     */
    public function implementInterface($interface)
    {
        if ($this->isInterfaceDefined($interface) === false) {
            throw Exception\ClassFile::interfaceNotFound($interface);
        }

        $this->implements[] = $interface;

        return $this;
    }

    /**
     * Use class/interface/trait
     *
     * @param string      $class
     * @param string|null $alias
     *
     * @return $this
     * @throws Exception\ClassUse
     */
    public function useClass($class, $alias = null)
    {
        $this->uses->addUse($class, $alias);

        return $this;
    }

    /**
     * Create a new class method
     *
     * @param string $name
     * @param bool   $overrideOnDefined
     *
     * @return Element\Method
     * @throws Exception\ClassFile      When the method is already defined and $overrideOnDefined = false
     */
    public function addMethod($name, $overrideOnDefined = false)
    {
        if ($this->methods->offsetExists($name) === true) {
            if ($overrideOnDefined === false) {
                throw Exception\ClassFile::methodAlreadyDefined($this->className, $name);
            }

            $this->methods->offsetUnset($name);
        }

        if (PHPUtils::isReservedAny($name) === true) {
            throw Exception\ClassFile::invalidMethodName($this->className, $name);
        }

        $method = new Element\Method($this, $name);
        $this->methods->offsetSet($name, $method);

        return $method;
    }

    /**
     * @param string      $name    The name of the property
     * @param string      $scope   The scope of the property. See self::SCOPE_*
     * @param string|null $default The default value. Should be a string, when you a text as default use ''
     *
     * @param bool        $overrideOnDefined
     *
     * @return Element\Property
     *
     * @throws Exception\ClassFile  When the property is already defined and your doesn't want to override it
     */
    public function addProperty($name, $scope = self::SCOPE_PROTECTED, $default = null, $overrideOnDefined = false)
    {
        if ($this->properties->offsetExists($name) === true) {
            if ($overrideOnDefined === false) {
                throw Exception\ClassFile::propertyAlreadyDefined($this->className, $name);
            }

            $this->properties->offsetUnset($name);
        }

        if (((bool) preg_match('/(^[^a-zA-Z_]|[^a-zA-Z0-9_])/', $name)) === true) {
            throw Exception\ClassFile::invalidPropertyName($this->className, $name);
        }

        $property = new Element\Property($this, $name, $scope, $default);
        $this->properties->offsetSet($name, $property);

        return $property;
    }

    /**
     * Add a new class constant
     *
     * @param string $name
     * @param string $value
     * @param bool   $overrideOnDefined
     *
     * @return Element\ClassConstant
     * @throws Exception\ClassFile
     */
    public function addConstant($name, $value, $overrideOnDefined = false)
    {
        if ($this->classConstants->offsetExists($name) === true) {
            if ($overrideOnDefined === false) {
                throw Exception\ClassFile::constantAlreadyDefined($this->className, $name);
            }

            $this->classConstants->offsetUnset($name);
        }

        if (
            PHPUtils::isReservedAny($name) === true ||
            (((bool) preg_match('/^[^a-zA-Z_]/', $name)) === true)
        ) {
            throw Exception\ClassFile::invalidConstantName($this->className, $name);
        }

        $classConstant = new Element\ClassConstant($name, $value);
        $this->classConstants->offsetSet($name, $classConstant);

        return $classConstant;
    }

    /**
     * Create a new trait
     *
     * @param $trait
     *
     * @return Element\ClassTrait
     * @throws Exception\ClassFile
     */
    public function addTrait($trait)
    {
        if ($this->isTraitDefined($trait) === false) {
            throw Exception\Type::isNotDefined($trait);
        }

        $trait = new Element\ClassTrait($this, $trait);
        $this->classTraits->append($trait);

        return $trait;
    }

    /**
     * Create property and setter method for the given name
     *
     * @param string $name      The name of the property
     * @param string $type      The type of the property see (self::PHP_TYPE_* or use class string)
     * @param null   $default   The default value of the property. For strings use ''
     *
     * @return $this
     */
    public function createSetter($name, $type = self::PHP_TYPE_MIXED, $default = null)
    {
        return $this->internalCreateGetterAndOrSetter($name, $type, $default, true, false);
    }

    /**
     * Create property and getter method for the given name
     *
     * @param string $name      The name of the property
     * @param string $type      The type of the property see (self::PHP_TYPE_* or use class string)
     * @param null   $default   The default value of the property. For strings use ''
     *
     * @return $this
     */
    public function createGetter($name, $type = self::PHP_TYPE_MIXED, $default = null)
    {
        return $this->internalCreateGetterAndOrSetter($name, $type, $default, false, true);
    }

    /**
     * Create property and (g|s)etter methods for the given name
     *
     * @param string $name      The name of the property
     * @param string $type      The type of the property see (self::PHP_TYPE_* or use class string)
     * @param null   $default   The default value of the property. For strings use ''
     *
     * @return $this
     */
    public function createGetterAndSetter($name, $type = self::PHP_TYPE_MIXED, $default = null)
    {
        return $this->internalCreateGetterAndOrSetter($name, $type, $default, true, true);
    }

    /**
     * Create property and/or methods for the given name
     *
     * @param string $name          The name of the property
     * @param string $type          The type of the property see (self::PHP_TYPE_* or use class string)
     * @param null   $default       The default value of the property. For strings use ''
     * @param bool   $createSetter
     * @param bool   $createGetter
     *
     * @return $this
     */
    protected function internalCreateGetterAndOrSetter(
        $name,
        $type         = self::PHP_TYPE_MIXED,
        $default      = null,
        $createSetter = false,
        $createGetter = false
    ) {
        $this->addProperty($name, static::SCOPE_PROTECTED, $default)->getDocBlock()->createVarTag($type, $name);

        if ($createSetter === true) {
            $methodName = "set" . ucfirst($name);
            $method     = $this->addMethod($methodName);
            $method->getDocBlock()->createReturnTag('self');
            $method->addArgument($name, $type, $default);
            $method->setContent("\$this->{$name} = \${$name};\n\nreturn \$this;");
        }

        if ($createGetter === true) {
            if ($type === static::PHP_TYPE_BOOLEAN) {
                $methodName = "is";
            } else {
                $methodName = "get";
            }

            $methodName .= ucfirst($name);

            $method     = $this->addMethod($methodName);
            $method->getDocBlock()->createReturnTag($type);
            $method->setContent("return \$this->{$name};");
        }

        return $this;
    }

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

            if ($this->isClassDefined($type) === true) {
                $inputs[$index] = $type . ($list ? '[]' : '');
                continue;
            }

            throw Exception\ClassFile::phpTypeDoesNotExists($type);
        }

        return implode('|', $inputs);
    }

    /**
     * Get the full class name of the given name (Trait, Interfaces included as well)
     *
     * @param  string $class
     *
     * @return string
     */
    public function getFullClassName($class)
    {
        // Rootclass
        if ($class[0] === '\\') {
            return $class;
        }

        $classParts = explode('\\', $class);
        $className  = array_pop($classParts);
        $firstPart  = array_shift($classParts);
        $middlePart = implode('\\', $classParts) ?: null;
        $found      = false;

        // Rewind the uses array
        $this->uses->rewind();

        // Check if the class has been 'used'
        foreach ($this->uses as $use) {
            if ($firstPart === null) {
                if ($use->getAlias() === $className || preg_match("/\\\\?{$className}$/", $use->getName())) {
                    // Mark class as 'found'
                    $found = true;

                    // The use is a full class include
                    $class = $use->getName();

                    break;
                }
            } else {
                if (preg_match("/{$firstPart}$/", $use->getAlias()) || preg_match("/{$firstPart}$/", $use->getName())) {
                    $class = "{$use->getName()}" . ($middlePart ? "\\{$middlePart}" : '') . "\\{$className}";
                    $found = true;
                    break;
                }
            }
        }

        // Nothing matches, check the class namespace
        if ($found === false) {
            $class = "{$this->namespace}\\{$class}";
        }

        // Check if the class exists
        return $class;
    }

    /**
     * Checks if the class is available from the full namespace or uses
     *
     * Before creating all the class elements / doc blocks make sure you add all the uses. When the uses are not set
     * the class will fail if it can not find the classes you want to use
     *
     * @param string $class
     *
     * @return bool
     */
    public function isClassDefined($class)
    {
        return ClassUtils::classExists($this->getFullClassName($class));
    }

    /**
     * Checks if the interface is available from the full namespace or uses
     *
     * Before creating all the class elements / doc blocks make sure you add all the uses. When the uses are not set
     * the class will fail if it can not find the classes you want to use
     *
     * @param string $interface
     *
     * @return bool
     */
    public function isInterfaceDefined($interface)
    {
        return ClassUtils::interfaceExists($this->getFullClassName($interface));
    }

    /**
     * Checks if the trait is available from the full namespace or uses
     *
     * Before creating all the class elements / doc blocks make sure you add all the uses. When the uses are not set
     * the class will fail if it can not find the classes you want to use
     *
     * @param string $trait
     *
     * @return bool
     */
    public function isTraitDefined($trait)
    {
        return ClassUtils::traitExists($this->getFullClassName($trait));
    }

    /**
     * Convert element to a PHP string
     *
     * @return string
     */
    public function __toString()
    {
        $file = "<?php\n";

        if ($this->namespace !== null) {
            $file .= "namespace {$this->namespace};\n\n";
        } else {
            $file .= "\n";
        }

        if ($this->uses->count() > 0) {

            $this->uses->uasort(function(Element\ClassUse $a, Element\ClassUse $b) {
                $aNamespace = strpos($a->getName(), '\\') !== false;
                $bNamespace = strpos($a->getName(), '\\') !== false;

                if ($a->getName() === $b->getName()) {
                    return 0;
                } else if ($aNamespace === true && $bNamespace === false) {
                    return 1;
                } else if ($aNamespace === false && $bNamespace === true) {
                    return -1;
                } else {
                    $array = array($a->getName(), $b->getName());
                    sort($array);

                    return ($array[0] === $a->getName()) ? -1 : 1;
                }
            });

            foreach ($this->uses as $use) {
                $file .= "use {$use->getName()}" . ($use->getAlias() ? " as {$use->getAlias()}" : '') . ";\n";
            }

            $file .= "\n";
        }

        // Create class docblock
        $docBlock = (string) $this->getClassDocBlock();
        if ($docBlock !== '') {
            $file .= "{$docBlock}\n";
        }

        // Check if the class should be abstract
        if ($this->abstractClass === true) {
            $file .= "abstract ";
        }

        // class type + name
        $file .= "{$this->classType} {$this->className}";

        // Check if there are any extends
        if (empty($this->extends) === false) {
            $file .= ' extends ' . implode(', ', $this->extends);
        }

        // Check if there are any implements
        if (empty($this->implements) === false) {
            $file .= ' implements ' . implode(', ', $this->implements);
        }

        // Open class
        $file .= "\n{";

        if ($this->classTraits->count() > 0) {
            foreach ($this->classTraits as $classTrait) {
                $file .= "\n";

                foreach (explode("\n", "{$classTrait}") as $line) {
                    $file .= "    {$line}\n";
                }

                $file .= "\n";
            }
        }

        if ($this->classConstants->count() > 0) {
            foreach ($this->classConstants as $classConstant) {
                $file .= "\n";

                foreach (explode("\n", "{$classConstant}") as $line) {
                    $file .= "    {$line}\n";
                }

                $file .= "\n";
            }
        }

        if ($this->properties->count() > 0) {
            foreach ($this->properties as $property) {
                $file .= "\n";

                foreach (explode("\n", "{$property}") as $line) {
                    $file .= "    {$line}\n";
                }

                $file .= "\n";
            }
        }

        if ($this->methods->count() > 0) {
            foreach ($this->methods as $method) {
                $file .= "\n";

                foreach (explode("\n", "{$method}") as $line) {
                    $file .= "    {$line}\n";
                }

                $file .= "\n";
            }
        }

        $file .= "}\n\n";

        return $file;
    }
}

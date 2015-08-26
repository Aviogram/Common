<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\PHPClass\ClassFile;
use Aviogram\Common\PHPClass\ElementInterface;
use Aviogram\Common\PHPClass\ScopeInterface;
use Aviogram\Common\PHPClass\ScopeTrait;
use Aviogram\Common\PHPClass\TypeInterface;
use Aviogram\Common\PHPClass\TypeTrait;

class Method implements ElementInterface, ScopeInterface, TypeInterface
{
    use ScopeTrait;
    use TypeTrait;

    /**
     * @var ClassFile
     */
    protected $classFile;

    /**
     * @var DocBlock
     */
    protected $docBlock;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $abstract = false;

    /**
     * @var boolean
     */
    protected $static = false;

    /**
     * @var string|null
     */
    protected $content;

    /**
     * @var Method\Arguments
     */
    protected $arguments;

    /**
     * Method constructor.
     *
     * @param ClassFile $classFile
     * @param string    $name
     */
    public function __construct(ClassFile $classFile, $name)
    {
        $this->classFile = $classFile;
        $this->name      = $name;
        $this->arguments = new Method\Arguments();
    }

    /**
     * @return DocBlock
     */
    public function getDocBlock()
    {
        return $this->docBlock ?: $this->docBlock = new DocBlock($this->classFile);
    }

    /**
     * @param  boolean $abstract
     *
     * @return $this
     */
    public function isAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    /**
     * @param  boolean $static
     *
     * @return $this
     */
    public function isStatic($static)
    {
        $this->static = $static;

        return $this;
    }

    /**
     * @param null|string $content
     *
     * @return Method
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string      $name         The name of the argument
     * @param string      $type         The type of the argument
     * @param null|string $default      The default value. When the default value is a string use '' around it.
     * @param null|string $description  The description for the doc block
     *
     * @return Method\Argument
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the given type does not exists
     */
    public function addArgument($name, $type, $default = null, $description = null)
    {
        $type     = $this->getType($type);
        $typeHint = null;

        switch ($type) {
            case static::PHP_TYPE_ARRAY:
                $typeHint = 'array';
                break;
            case static::PHP_TYPE_CALLABLE:
                $typeHint = '\closure';
                break;
        }

        if ($typeHint === null) {
            if (substr($type, -2) === '[]') {
                $typeHint = 'array';
            } else if ($this->isClassDefined($type) === true) {
                $typeHint = $type;
            }
        }

        // Create new argument
        $argument = new Method\Argument($name, $typeHint, $default);
        $this->arguments->append($argument);

        // Add argument to the docBlock
        $this->getDocBlock()->createParamTag($type, $name, $description);

        return $argument;
    }

    /**
     *  Checks if the class is defined or not
     *
     * @param  string $class
     *
     * @return boolean
     */
    protected function isClassDefined($class)
    {
        return $this->classFile->isClassDefined($class);
    }

    /**
     *  Checks if the interface is defined or not
     *
     * @param  string $interface
     *
     * @return boolean
     */
    protected function isInterfaceDefined($interface)
    {
        return $this->classFile->isInterfaceDefined($interface);
    }

    /**
     *  Checks if the trait is defined or not
     *
     * @param  string $trait
     *
     * @return boolean
     */
    protected function isTraitDefined($trait)
    {
        return $this->classFile->isTraitDefined($trait);
    }

    /**
     * Convert element to a PHP string
     *
     * @return string
     */
    public function __toString()
    {
        $string = "{$this->getDocBlock()}";

        if ($string !== '') {
            $string .= "\n";
        }

        if ($this->abstract === true) {
            $string .= 'abstract ';
        }

        $string .= "{$this->getScope()}";

        if ($this->static === true) {
            $string .= ' static';
        }

        $string .= " function {$this->name}(";

        if ($this->arguments->count() > 0) {
            $arguments = array();

            foreach ($this->arguments as $argument) {
                $arguments[] = "{$argument}";
            }

            $string .= implode(', ', $arguments);
        }

        $string .= ")";

        if ($this->abstract === true) {
            return $string . ';';
        }

        $string .= "\n{";

        if ($this->content !== null) {
            $string .= "\n";

            foreach (explode("\n", $this->content) as $line) {
                $string .= "    {$line}\n";
            }
        }

        return $string . "}";
    }
}

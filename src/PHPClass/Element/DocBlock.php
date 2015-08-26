<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\PHPClass\ClassFile;
use Aviogram\Common\PHPClass\Element\DocBlock\TagColumnLength;
use Aviogram\Common\PHPClass\ElementInterface;
use ReflectionClass;

class DocBlock implements ElementInterface
{
    /**
     * @var ClassFile
     */
    protected $classFile;

    /**
     * @var DocBlock\Tags
     */
    protected $tags;

    /**
     * @var null|string
     */
    protected $summary;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * @var TagColumnLength
     */
    protected $maxColumnLengths;

    /**
     * @var ReflectionClass
     */
    protected $tagPrototype;

    /**
     * DocBlock constructor.
     *
     * @param ClassFile $classFile
     */
    public function __construct(ClassFile $classFile)
    {
        $this->classFile        = $classFile;
        $this->tags             = new DocBlock\Tags();
        $this->tagPrototype     = new ReflectionClass(__NAMESPACE__ . '\DocBlock\Tag');
        $this->maxColumnLengths = new TagColumnLength();
    }

    /**
     * @return null|string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param null|string $summary
     *
     * @return DocBlock
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * The short description. (First line of the doc block)
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * The full description of an element
     *
     * @param null|string $description
     *
     * @return DocBlock
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DocBlock
     */
    public function createAPITag()
    {
        return $this->internalCreateTag(true, 'api');
    }

    /**
     * @param string      $name
     * @param null|string $email
     *
     * @return DocBlock
     */
    public function createAuthorTag($name, $email = null)
    {
        return $this->internalCreateTag(true, 'author', $name, $email ? "<$email>" : null);
    }

    /**
     * @param  string $description
     *
     * @return DocBlock
     */
    public function createCopyrightTag($description)
    {
        return $this->internalCreateTag(true, 'copyright', $description);
    }

    /**
     * @param null|string $version      Till which version this method will exists
     * @param null|string $description  More explaination about the deprecation
     *
     * @return DocBlock
     */
    public function createDeprecatedTag($version = null, $description = null)
    {
        return $this->internalCreateTag(true, 'deprecated', $version, $description);
    }

    /**
     * @param string      $location         Location the example file
     * @param null|string $startLine        The line number where the example starts
     * @param null|string $numberOfLines    The number of lines for the example
     * @param null|string $description      The description about the example
     *
     * @return DocBlock
     */
    public function createExampleTag($location, $startLine = null, $numberOfLines = null, $description = null)
    {
        return $this->internalCreateTag(true, 'example', $location, $startLine, $numberOfLines, $description);
    }

    /**
     * @return DocBlock
     */
    public function createFileSourceTag()
    {
        return $this->internalCreateTag(true, 'filesource');
    }

    /**
     * Ignore processing
     *
     * @return DocBlock
     */
    public function createIgnoreTag()
    {
        return $this->internalCreateTag(true, 'ignore');
    }

    /**
     * Tells that this part is used internally so no external usage is not a smart idea.
     *
     * @param null|string $description
     *
     * @return DocBlock
     */
    public function createInternalTag($description = null)
    {
        return $this->internalCreateTag(true, 'internal', $description);
    }


    /**
     * Create a license tag
     *
     * @param string      $name
     * @param null|string $url
     *
     * @return DocBlock
     */
    public function createLicenseTag($name, $url = null)
    {
        return $this->internalCreateTag(true, 'license', $url, $name);
    }

    /**
     * Link the element to a website
     *
     * @param string      $url
     * @param null|string $description
     *
     * @return DocBlock
     */
    public function createLinkTag($url, $description = null)
    {
        return $this->internalCreateTag(true, 'link', $url, $description);
    }

    /**
     * @param string $returnType    The return type see ClassFile::PHP_TYPE_*
     * @param string $name          The name of the method
     * @param array  $arguments     ['argumentName' => 'argumentType'], leave argumentType NULL when no type available
     * @param null   $description
     *
     * @return DocBlock
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the type does not exists or the class does not exists
     */
    public function createMethodTag($returnType, $name, array $arguments = array(), $description = null)
    {
        $stringArguments = array();
        foreach ($arguments as $argumentName => $argumentType) {
            if ($argumentType !== null) {
                $argumentType = $this->classFile->getType($argumentType);
            }

            $stringArguments[] = ($argumentType ? "{$argumentType} " : '') . "\${$argumentName}";
        }

        $returnType = $this->classFile->getType($returnType);
        $name       = "{$name}(" . implode(', ', $stringArguments) . ')';

        return $this->internalCreateTag(false, 'method', $returnType, $name, $description);
    }

    /**
     * Used to logically categorize the package elements instead of the PHP namespaces
     *
     * @param  string $packageNamespace
     *
     * @return DocBlock
     */
    public function createPackageTag($packageNamespace)
    {
        return $this->internalCreateTag(true, 'package', $packageNamespace);
    }

    /**
     * Create a param tag for describing method arguments
     *
     * @param string      $type         The type of the argument (See FileClass::PHP_TYPE_*) or use class name
     * @param string      $name         The name of the parameters (method argument)
     * @param null|string $description  The description with the argument
     *
     * @return DocBlock
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the type does not exists or the class does not exists
     */
    public function createParamTag($type, $name, $description = null)
    {
        return $this->createVariableTag('param', $type, $name, $description);
    }

    /**
     * Create a property tag for describing class properties
     *
     * @param string      $type         The type of the property (See FileClass::PHP_TYPE_*) or use class name
     * @param string      $name         The name of the property
     * @param null|string $description  The description with the property
     *
     * @return DocBlock
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the type does not exists or the class does not exists
     */
    public function createPropertyTag($type, $name, $description = null)
    {
        return $this->createVariableTag('property', $type, $name, $description);
    }

    /**
     * Create a property tag for describing class properties (read-only)
     *
     * @param string      $type         The type of the property (See FileClass::PHP_TYPE_*) or use class name
     * @param string      $name         The name of the property
     * @param null|string $description  The description with the property
     *
     * @return DocBlock
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the type does not exists or the class does not exists
     */
    public function createPropertyReadTag($type, $name, $description = null)
    {
        return $this->createVariableTag('property-read', $type, $name, $description);
    }

    /**
     * Create a property tag for describing class properties (write-only)
     *
     * @param string      $type         The type of the property (See FileClass::PHP_TYPE_*) or use class name
     * @param string      $name         The name of the property
     * @param null|string $description  The description with the property
     *
     * @return DocBlock
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the type does not exists or the class does not exists
     */
    public function createPropertyWriteTag($type, $name, $description = null)
    {
        return $this->createVariableTag('property-write', $type, $name, $description);
    }

    /**
     * Define the return type of a method
     *
     * @param string      $type
     * @param null|string $description
     *
     * @return DocBlock
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the type does not exists or the class does not exists
     */
    public function createReturnTag($type, $description = null)
    {
        return $this->internalCreateTag(true, 'return', $this->classFile->getType($type), $description);
    }

    /**
     * Reference to an element or website with the documentation (two-way)
     *
     * @param string $urlOrFQSEN    Link to the documentation or link to element
     * @param null   $description
     *
     * @return DocBlock
     */
    public function createSeeTag($urlOrFQSEN, $description = null)
    {
        return $this->internalCreateTag(false, 'see', $urlOrFQSEN, $description);
    }

    /**
     * Since when the element becomes available
     *
     * @param string      $version
     * @param null|string $description
     *
     * @return DocBlock
     */
    public function createSinceTag($version, $description = null)
    {
        return $this->internalCreateTag(true, 'since', $version, $description);
    }

    /**
     * Indicates if the element throws partical exceptions
     *
     * @param string      $exceptionClass   The classname of the exception
     * @param null|string $description
     *
     * @return DocBlock
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the class does not exists
     */
    public function createThrowsTag($exceptionClass, $description = null)
    {
        return $this->internalCreateTag(false, 'throws', $this->classFile->getType($exceptionClass), $description);
    }

    /**
     * Mark the element as unfinished
     *
     * @param string $description
     *
     * @return DocBlock
     */
    public function createTodoTag($description)
    {
        return $this->internalCreateTag(true, 'todo', $description);
    }

    /**
     * Reference to an element or website with the documentation (one-way)
     *
     * @param string        $fqsen         Link to the element
     * @param null|string   $description
     *
     * @return DocBlock
     */
    public function createUsesTag($fqsen, $description = null)
    {
        return $this->internalCreateTag(false, 'see', $fqsen, $description);
    }

    /**
     * Create a var tag for describing class properties
     *
     * @param string      $type         The type of the property (See FileClass::PHP_TYPE_*) or use class name
     * @param string      $name         The name of the property
     * @param null|string $description  The description with the property
     *
     * @return DocBlock
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the type does not exists or the class does not exists
     */
    public function createVarTag($type, $name, $description = null)
    {
        return $this->createVariableTag('var', $type, $name, $description);
    }

    /**
     * Indicates the current version of the element
     *
     * @param string      $version
     * @param null|string $description
     *
     * @return DocBlock
     */
    public function createVersionTag($version, $description = null)
    {
        return $this->internalCreateTag(true, 'version', $version, $description);
    }

    /**
     * @param string      $name             The name of the tag
     * @param bool        $replaceOnExists  When TRUE there will be one tag instance for the given name
     * @param null|string $column1          The value of the first column
     * @param null|string $column2          The value of the second column
     * @param null|string $_                The value of the nth column
     *
     * @return DocBlock
     */
    public function createCustomTag($name, $replaceOnExists = true, $column1 = null, $column2 = null, $_ = null)
    {
        // Grep the arguments and change the order for the internal method
        $arguments = func_get_args();
        array_shift($arguments);
        array_shift($arguments);

        array_unshift($arguments, $name);
        array_unshift($arguments, $replaceOnExists);

        return call_user_func_array(array($this, 'internalCreateTag'), $arguments);
    }

    /**
     * Combine couple of the same tags with a different name/purpose
     *
     * @param string      $tagName      The name of the tag
     * @param string      $type         The type of the property (See FileClass::PHP_TYPE_*) or use class name
     * @param string      $name         The name of the property
     * @param null|string $description  The description with the property
     *
     * @return DocBlock
     *
     * @throws \Aviogram\Common\PHPClass\Exception\ClassFile    When the type does not exists or the class does not exists
     */
    protected function createVariableTag($tagName, $type, $name, $description = null)
    {
        return $this->internalCreateTag(false, $tagName, $this->classFile->getType($type), "\${$name}", $description);
    }

    /**
     * Create tag with the given values
     *
     * @param boolean       $single     Whether there can be one instance of the tag or not
     * @param string        $name
     * @param null|string   $column1
     * @param null|string   $column2
     * @param null|string   $_
     *
     * @return DocBlock
     */
    protected function internalCreateTag($single = true, $name, $column1 = null, $column2 = null, $_ = null)
    {
        // Create constructor arguments
        $arguments = func_get_args();
        $single    = array_shift($arguments);

        array_unshift($arguments, $this->maxColumnLengths);

        // Remove null values
        foreach ($arguments as $index => $argument) {
            if ($argument === null) {
                unset($arguments[$index]);
            }
        }

        $arguments = array_values($arguments);
        $tag       = $this->tagPrototype->newInstanceArgs($arguments);

        // Create new tag
        if ($single === true) {
            $this->tags->offsetSet($arguments[1], $tag);
        } else {
            $this->tags->append($tag);
        }

        return $this;
    }

    /**
     * Convert element to a PHP string
     *
     * @return string
     */
    public function __toString()
    {
        // When nothing is defined return an empty string
        if ($this->summary === null && $this->description === null && $this->tags->count() === 0) {
            return '';
        }

        $string = "/**\n";

        if ($this->getSummary() !== null) {
            $string .= " * {$this->getSummary()}\n";
        }

        if ($this->getDescription() !== null) {
            if ($this->getSummary() !== null) {
                $string .= " *\n";
            }

            foreach (explode("\n", $this->getDescription()) as $line) {
                $string .= " * {$line}\n";
            }
        }

        // Sort tags by priority and than by name
        $this->tags->uasort(function(DocBlock\Tag $a, DocBlock\Tag $b) {
            if ($a->getPriority() > $b->getPriority()) {
                return -1;
            } else if ($a->getPriority() < $b->getPriority()) {
                return 1;
            } else if ($a->getName() === $b->getName()) {
                return 1;
            } else {
                $array = array($a->getName(), $b->getName());
                sort($array);

                return ($array[0] === $a->getName()) ? -1 : 1;
            }
        });

        $lastTag = null;
        foreach ($this->tags as $tag) {
            if ($lastTag !== $tag->getName() && $string !== "/**\n") {
                $string .= " *\n";
                $lastTag = $tag->getName();
            }

            $string .= " * {$tag}\n";
        }

        return "{$string} */";
    }
}

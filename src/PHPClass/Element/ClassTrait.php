<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\PHPClass\ClassFile;
use Aviogram\Common\PHPClass\ElementInterface;

class ClassTrait implements ElementInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $secondNames = array();

    /**
     * @var ClassFile
     */
    protected $classFile;

    /**
     * @var array
     */
    protected $conflicts = array();

    /**
     * @param ClassFile $classFile
     * @param string    $fullClassName
     */
    public function __construct(ClassFile $classFile, $fullClassName)
    {
        $this->classFile = $classFile;
        $this->name      = $fullClassName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Resolve method name conflict with ignoring one trait over the other
     *
     * @param string $trait          The trait that will be used
     * @param string $method         The method of the trait which causes the conflict
     * @param string $insteadOfTrait The trait which will be ignored
     *
     * @return $this
     * @throws \Aviogram\Common\PHPClass\Exception\ClassTrait   When the base trait is not used
     * @throws \Aviogram\Common\PHPClass\Exception\ClassTrait   When the method does not exists
     */
    public function resolveConflictInsteadOf($trait, $method, $insteadOfTrait)
    {
        $fullClass = $this->classFile->getFullClassName($trait);
        if (method_exists($fullClass, $method) === false) {
            throw \Aviogram\Common\PHPClass\Exception\ClassTrait::undefinedMethod($trait, $method);
        }

        if ($trait !== $this->name && $insteadOfTrait !== $this->name) {
            throw \Aviogram\Common\PHPClass\Exception\ClassTrait::knownTraitNotUsedByResolveInsteadOf($this->name);
        } else if ($trait !== $this->name && in_array($trait, $this->secondNames) === false) {
            if ($this->classFile->isClassDefined($trait) === false) {
                throw \Aviogram\Common\PHPClass\Exception\ClassTrait::traitNotFound($trait);
            }

            $this->secondNames[] = $trait;
        } else if ($insteadOfTrait !== $this->name && in_array($insteadOfTrait, $this->secondNames) === false) {
            if ($this->classFile->isClassDefined($insteadOfTrait) === false) {
                throw \Aviogram\Common\PHPClass\Exception\ClassTrait::traitNotFound($insteadOfTrait);
            }

            $this->secondNames[] = $insteadOfTrait;
        }

        $this->conflicts[] = "{$trait}::{$method} insteadOf {$insteadOfTrait};";

        return $this;
    }

    /**
     * Resolve method name conflict with aliasing the method name
     *
     * @param string $trait  The trait that causing the conflict
     * @param string $method The method of the trait that causing the conflict
     * @param string $alias  The alias for the method
     *
     * @return $this
     * @throws \Aviogram\Common\PHPClass\Exception\ClassTrait   When the trait does not exists
     * @throws \Aviogram\Common\PHPClass\Exception\ClassTrait   When the method is not defined on the trait
     */
    public function resolveConflictAlias($trait, $method, $alias)
    {
        $fullClass = $this->classFile->getFullClassName($trait);
        if (method_exists($fullClass, $method) === false) {
            throw \Aviogram\Common\PHPClass\Exception\ClassTrait::undefinedMethod($trait, $method);
        }

        $this->conflicts[] = "{$trait}::{$method} as {$alias};";

        if ($trait !== $this->name && in_array($trait, $this->secondNames) === false) {
            if ($this->classFile->isTraitDefined($trait) === false) {
                throw \Aviogram\Common\PHPClass\Exception\ClassTrait::traitNotFound($trait);
            }

            $this->secondNames[] = $trait;
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
        $traits = $this->secondNames;
        array_unshift($traits, $this->name);

        $string = 'use ' . implode(', ', $traits);
        if (empty($this->conflicts) === false) {
            $string .= " {\n";

            foreach ($this->conflicts as $conflict) {
                $string .= "    {$conflict}\n";
            }

            $string .= "}";
        } else {
            $string .= ";";
        }

        return $string;
    }
}

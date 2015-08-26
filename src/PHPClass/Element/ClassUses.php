<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\AbstractCollection;
use Aviogram\Common\PHPClass\ClassFile;
use Aviogram\Common\PHPClass\Exception;

/**
 * @method ClassUse current()
 * @method ClassUse offsetGet($index)
 */
class ClassUses extends AbstractCollection
{
    /**
     * @var ClassFile
     */
    protected $classFile;

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Construct an ArrayIterator
     *
     * @link http://php.net/manual/en/arrayiterator.construct.php
     *
     * @param array $array The array or object to be iterated on.
     * @param int   $flags Flags to control the behaviour of the ArrayObject object.
     *
     * @throws Exception\Collection When the given array contains invalid values
     * @see  ArrayObject::setFlags()
     */
    public function __construct(ClassFile $classFile, $array = array(), $flags = 0)
    {
        parent::__construct($array, $flags);

        $this->classFile = $classFile;
    }

    /**
     * Determines of the value is a valid collection value
     *
     * @param  mixed $value
     *
     * @return boolean
     */
    protected function isValidValue($value)
    {
        return ($value instanceof ClassUse);
    }

    /**
     * @param string      $class
     * @param null|string $alias
     * @param bool        $checkClassExists Use FALSE when you want to use a namespace instead of a class
     *
     * @return $this
     * @throws Exception\ClassUse  When the given class does not exists
     */
    public function addUse($class, $alias = null, $checkClassExists = true)
    {
        if (
            $checkClassExists === true &&
            $this->classFile->isClassDefined("\\{$class}") === false &&
            $this->classFile->isInterfaceDefined("\\{$class}") === false &&
            $this->classFile->isTraitDefined("\\{$class}") === false
        ) {
            throw Exception\ClassUse::classUseNotFound($class);
        }

        $use = new ClassUse($class, $alias);

        $this->append($use);

        return $this;
    }
}

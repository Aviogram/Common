<?php
namespace Aviogram\Common\Hydrator;

use Aviogram\Common\Hydrator\Exception\EntityNotExists;

class ByConfigBuilder
{
    /**
     * @var string
     */
    protected $entityClassName;

    /**
     * @var bool
     */
    protected $isCollection;

    /**
     * @var string
     */
    protected $collectionClassName;

    /**
     * @var array
     */
    protected static $entities = array();

    /**
     * @var array
     */
    protected $mapping = array();

    /**
     * @var string | NULL
     */
    protected $catchAllSetter;

    /**
     * @var array
     */
    private $exceptions = array(
        1 => 'Entity with class name `%s` does not exists.',
        2 => 'Method `%s` does not exists on entity `%s`',
        3 => 'CollectionClass should be either ArrayIterator or a class that extends the ArrayIterator class',
        4 => 'Could not create config with no entity and no collection specified',
    );

    /**
     * @param string $entityClassName
     * @param bool   $isCollection
     * @param string $collectionClassName
     */
    public function __construct($entityClassName = null, $isCollection = false, $collectionClassName = 'ArrayIterator')
    {
        $this->entityClassName     = $entityClassName;
        $this->isCollection        = $isCollection;
        $this->collectionClassName = $collectionClassName;

        if ($entityClassName === null && $isCollection === false) {
            throw new Exception\ConfigFailed($this->exceptions[4], 4);
        }

        if ($entityClassName !== null) {
            if (array_key_exists($this->entityClassName, static::$entities) === false) {
                if (class_exists($this->entityClassName) === false) {
                    throw new Exception\ConfigFailed(sprintf($this->exceptions[1], $this->entityClassName), 1);
                }

                static::$entities[$this->entityClassName] = new $this->entityClassName();
            }
        }

        if ($this->isCollection === true) {
            if (is_subclass_of($collectionClassName, 'ArrayIterator') === false) {
                throw new Exception\ConfigFailed(sprintf($this->exceptions[3], $this->collectionClassName), 3);
            }
        }
    }

    /**
     * Set a mapping
     *
     * @param  string          $fieldName
     * @param  string          $getMethod
     * @param  string          $setMethod
     * @param  ByConfigBuilder $include
     * @param  Closure         $formatter
     *
     * @return $this
     */
    public function addField($fieldName, $getMethod, $setMethod, ByConfigBuilder $include = null, $formatter = null)
    {
        if (method_exists($this->getEntityPrototype(), $getMethod) === false) {
            throw new Exception\ConfigFailed(sprintf($this->exceptions[2], $getMethod, $this->entityClassName), 2);
        }

        if (method_exists($this->getEntityPrototype(), $setMethod) === false) {
            throw new Exception\ConfigFailed(sprintf($this->exceptions[2], $setMethod, $this->entityClassName), 2);
        }

        $this->mapping[$fieldName] = array(
            'getter'    => $getMethod,
            'setter'    => $setMethod,
            'include'   => $include,
            'formatter' => $formatter,
        );

        return $this;
    }

    /**
     * Returns if the given fieldName exists
     *
     * @param  string $fieldName
     *
     * @return bool
     */
    public function hasField($fieldName)
    {
        return array_key_exists($fieldName, $this->mapping);
    }

    /**
     * Check if the given fieldName is an include
     *
     * @param  string $fieldName
     *
     * @return bool TRUE on include | FALSE when field does not exists or field is not a include
     */
    public function isInclude($fieldName)
    {
        if ($this->hasField($fieldName) === false) {
            return false;
        }

        $class = __CLASS__;

        return ($this->mapping[$fieldName]['include'] instanceof $class);
    }

    /**
     * Return the setter name
     *
     * @param  string $fieldName
     *
     * @return string | bool FALSE when field does not exists
     */
    public function getSetter($fieldName)
    {
        if ($this->hasField($fieldName) === false) {
            return false;
        }

        return $this->mapping[$fieldName]['setter'];
    }

    /**
     * Return the getter name
     *
     * @param  string $fieldName
     *
     * @return string | bool FALSE when field does not exists
     */
    public function getGetter($fieldName)
    {
        if ($this->hasField($fieldName) === false)
        {
            return false;
        }
        return $this->mapping[$fieldName]['getter'];
    }

    /**
     * Get the include config
     *
     * @param  string $fieldName
     *
     * @return ByConfigBuilder | bool FALSE when fieldName is not a include
     */
    public function getInclude($fieldName)
    {
        if ($this->isInclude($fieldName) === false) {
            return false;
        }

        return $this->mapping[$fieldName]['include'];
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return $this->isCollection;
    }

    /**
     * @param  string $fieldName
     *
     * @return Closure
     */
    public function getFormatter($fieldName)
    {
        if ($this->hasField($fieldName) === false) {
            return null;
        }

        return $this->mapping[$fieldName]['formatter'];
    }

    /**
     * @param  array $data this will
     *
     * @return \ArrayIterator | boolean Returns FALSE when this config is not a collection configuration
     */
    public function getCollectionEntity(array $data = array())
    {
        if ($this->isCollection() === false) {
            return false;
        }

        return new $this->collectionClassName($data);
    }

    /**
     * @return object | FALSE when no entity has been specified
     */
    public function getEntity()
    {
        if ($this->entityClassName === null) {
            return false;
        }

        return new $this->entityClassName();
    }

    /**
     * Sets an catchall Setter
     *
     * @param  string $setMethod
     *
     * @return $this
     */
    public function setCatchAllSetter($setMethod)
    {
        if (method_exists($this->getEntityPrototype(), $setMethod) === false) {
            throw new Exception\ConfigFailed(sprintf($this->exceptions[2], $setMethod, $this->entityClassName), 2);
        }

        $this->catchAllSetter = $setMethod;

        return $this;
    }

    /**
     * @return NULL|string
     */
    public function getCatchAllSetter()
    {
        return $this->catchAllSetter;
    }

    /**
     * @return object
     */
    protected function getEntityPrototype()
    {
        return static::$entities[$this->entityClassName];
    }
}

<?php
namespace Aviogram\Common;

abstract class AbstractCollection extends \ArrayIterator implements \JsonSerializable
{
    /**
     * When TRUE you cannot add/remove entities from the collection
     *
     * @var bool
     */
    private $readOnly = false;

    /**
     * Determines of the value is a valid collection value
     *
     * @param  mixed   $value
     * @return boolean
     */
    abstract protected function isValidValue($value);

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
    public function __construct($array = array(), $flags = 0)
    {
        foreach ($array as $value) {
            $this->isValidValue($value);

            throw Exception\Collection::invalidType($value, get_class($this));
        }

        parent::__construct($array, $flags);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Set value for an offset
     *
     * @link http://php.net/manual/en/arrayiterator.offsetset.php
     *
     * @param string $index  <p>
     *                       The index to set for.
     *                       </p>
     * @param string $newval <p>
     *                       The new value to store at the index.
     *                       </p>
     *
     * @throws Exception\Collection When the collection has been marked as read-only
     * @throws Exception\Collection When the given type is not correct
     */
    public function offsetSet($index, $newval)
    {
        if ($this->isReadOnly() === true) {
            throw Exception\Collection::readOnly(get_class($this));
        }

        if ($this->isValidValue($newval) === false) {
            throw Exception\Collection::invalidType($newval, get_class($this));
        }

        parent::offsetSet($index, $newval);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Unset value for an offset
     *
     * @link http://php.net/manual/en/arrayiterator.offsetunset.php
     *
     * @param string $index <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @throws Exception\Collection When the collection has been marked as read-only
     */
    public function offsetUnset($index)
    {
        if ($this->isReadOnly() === true) {
            throw Exception\Collection::readOnly(get_class($this));
        }

        parent::offsetUnset($index);
    }

    /**
     * When TRUE it is not possible to alter the collection
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * Set the collection to read only
     *
     * @return $this
     */
    public function readOnly()
    {
        $this->readOnly = true;

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}

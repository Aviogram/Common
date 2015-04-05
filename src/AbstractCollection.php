<?php
namespace Aviogram\Common;

use InvalidArgumentException;

abstract class AbstractCollection extends \ArrayIterator implements \JsonSerializable
{
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
     * @link http://php.net/manual/en/arrayiterator.construct.php
     * @param array $array The array or object to be iterated on.
     * @param int $flags Flags to control the behaviour of the ArrayObject object.
     * @see ArrayObject::setFlags()
     *
     * @throws InvalidArgumentException When the value is not valid
     */
    public function __construct($array = array(), $flags = 0)
    {
        foreach ($array as $value) {
            $this->isValidValue($value);
        }

        parent::__construct($array, $flags);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Set value for an offset
     * @link http://php.net/manual/en/arrayiterator.offsetset.php
     * @param string $index <p>
     * The index to set for.
     * </p>
     * @param string $newval <p>
     * The new value to store at the index.
     * </p>
     * @return void
     *
     * @throws InvalidArgumentException When the given value is not valid
     */
    public function offsetSet($index, $newval)
    {
        if ($this->isValidValue($newval) === false) {
            throw new InvalidArgumentException('Adding a value of the wrong type.');
        }

        return parent::offsetSet($index, $newval);
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

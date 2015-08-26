<?php
namespace Aviogram\Common\PHPClass\Element\DocBlock;

use ArrayIterator;

class TagColumnLength
{
    /**
     * @var ArrayIterator
     */
    protected $columns;

    /**
     * Initialize the TagColumn length
     */
    public function __construct()
    {
        $this->columns = new ArrayIterator();
    }

    /**
     * Set the max column lengths for later use
     *
     * @param null $column1
     * @param null $column2
     * @param null $_
     *
     * @return self
     */
    public function setMaxLengths($column1 = null, $column2 = null, $_ = null)
    {
        foreach (func_get_args() as $index => $argument) {
            $length = strlen((string) $argument);

            if ($this->columns->offsetExists($index) === false || $this->columns->offsetGet($index) < $length) {
                $this->columns->offsetSet($index, $length);
            }
        }

        return $this;
    }

    /**
     * Get the padding needed based on the max column length and the column value given
     *
     * @param integer $columnIndex
     * @param string  $columnValue
     *
     * @return integer
     */
    public function getColumnPaddingLength($columnIndex, $columnValue)
    {
        if ($this->columns->offsetExists($columnIndex) === false) {
            return 0;
        }

        return $this->columns->offsetGet($columnIndex) - strlen($columnValue);
    }
}

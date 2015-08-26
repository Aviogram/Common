<?php
namespace Aviogram\Common\PHPClass\Element\DocBlock;

use Aviogram\Common\PHPClass\ElementInterface;

class Tag implements ElementInterface
{
    /**
     * @var TagColumnLength
     */
    protected $columnLengths;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $columnValues = array();

    /**
     * @var int
     */
    protected $priority = 1;

    /**
     * Create a new
     *
     * @param TagColumnLength $length   Keeps track of the column lengths for string paddings
     * @param string          $name     The name of the tag
     * @param null            $value1   The first tag argument value
     * @param null            $value2   The second tag argument value
     * @param null            $_        The N tag argument value
     */
    public function __construct(TagColumnLength $length, $name, $value1 = null, $value2 = null, $_ = null)
    {
        // Get all the arguments
        $columnValues        = func_get_args();

        // Remove first argument. Does not belong to the tag
        $this->columnLengths = array_shift($columnValues);

        // Create column values
        $columnValues       = array_values($columnValues);
        $columnValues[0]    = "@{$columnValues[0]}";

        // Set the column values
        $this->columnValues = $columnValues;

        // The tag name
        $this->name         = $name;

        // Set the column lengths
        call_user_func_array(array($this->columnLengths, 'setMaxLengths'), $columnValues);
    }

    /**
     * Get the priority of the element. How higher the priority will be first rendered
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Convert element to a PHP string
     *
     * @return string
     */
    public function __toString()
    {
        $string = '';

        // Loop through all the columns
        foreach ($this->columnValues as $index => $value) {
            $padding  = $this->columnLengths->getColumnPaddingLength($index, $value) + 1;
            $string  .= $value . str_repeat(' ', $padding);
        }

        // Remove most right padding and return the string
        return rtrim($string);
    }
}

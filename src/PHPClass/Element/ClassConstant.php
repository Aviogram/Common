<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\PHPClass\ElementInterface;

class ClassConstant implements ElementInterface
{
    const _CONST_2 = 1;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $name    The name of the constant
     * @param string $value   The value of the constant. Use '' for a string.
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * Convert element to a PHP string
     *
     * @return string
     */
    public function __toString()
    {
        return "const {$this->name} = {$this->value};";
    }
}

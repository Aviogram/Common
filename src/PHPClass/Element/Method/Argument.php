<?php
namespace Aviogram\Common\PHPClass\Element\Method;

use Aviogram\Common\PHPClass\ElementInterface;

class Argument implements ElementInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $typeHint;

    /**
     * @var string|null
     */
    protected $default;

    /**
     * @param string      $name
     * @param null|string $typeHint
     * @param null|string $default
     */
    public function __construct($name, $typeHint = null, $default = null)
    {
        $this->name     = $name;
        $this->typeHint = $typeHint;
        $this->default  = $default;
    }

    /**
     * Convert element to a PHP string
     *
     * @return string
     */
    public function __toString()
    {
        return ($this->typeHint ? "{$this->typeHint} " : '') . "\${$this->name}" . ($this->default ? " = {$this->default}" : '');
    }
}

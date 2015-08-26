<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\PHPClass\ElementInterface;

class ClassUse implements ElementInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $alias;

    /**
     * @param string      $fullClassName
     * @param null|string $alias
     */
    public function __construct($fullClassName, $alias = null)
    {
        $this->name  = $fullClassName;
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Convert element to a PHP string
     *
     * @return string
     */
    public function __toString()
    {
        return "use {$this->name}" . ($this->alias ? " as {$this->alias}" : '') . ';';
    }
}

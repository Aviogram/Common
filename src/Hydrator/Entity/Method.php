<?php
namespace Aviogram\Common\Hydrator\Entity;

class Method
{
    /**
     * @var boolean
     */
    protected $required;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var string
     */
    protected $typehint;

    /**
     * @var boolean
     */
    protected $child;

    /**
     * Constructor of the method entity
     *
     * @param boolean $required
     * @param mixed   $default
     * @param string  $typehint
     * @param boolean $child
     */
    public function __construct($required, $default, $typehint, $child)
    {
        $this->required = $required;
        $this->default  = $default;
        $this->typehint = $typehint;
        $this->child    = $child;
    }

    /**
     * Shows if the argument is required or not
     *
     * @return boolean
     */
    public function isRequired()
    {
        return (bool) $this->required;
    }

    /**
     * Get the default value of the argument
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Check if the typehint is an array
     *
     * @return boolean
     */
    public function isTypehintArray()
    {
        return (bool) ($this->typehint === 'array');
    }

    /**
     * Get the typehint of the first argument
     *
     * @return string
     */
    public function getTypehint()
    {
        return $this->typehint;
    }

    /**
     * Wheter the method accepts child hydration
     *
     * @return boolean
     */
    public function hasChild()
    {
        return $this->child;
    }
}

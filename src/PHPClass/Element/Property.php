<?php
namespace Aviogram\Common\PHPClass\Element;

use Aviogram\Common\PHPClass\ClassFile;
use Aviogram\Common\PHPClass\ElementInterface;
use Aviogram\Common\PHPClass\ScopeInterface;
use Aviogram\Common\PHPClass\ScopeTrait;

class Property implements ElementInterface, ScopeInterface
{
    use ScopeTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $default;

    /**
     * @var DocBlock
     */
    protected $docBlock;

    /**
     * @param ClassFile   $classFile
     * @param string      $name    The name of the property
     * @param string      $scope   The scope of the property. See self::SCOPE_*
     * @param string|null $default The default value. Should be a string, when you a text as default use ''
     *
     * @throws \Aviogram\Common\PHPClass\Exception\Scope
     */
    public function __construct(ClassFile $classFile, $name, $scope = self::SCOPE_PROTECTED, $default = null)
    {
        $this->name      = $name;
        $this->default   = $default;
        $this->docBlock  = new DocBlock($classFile);

        $this->setScope($scope);
    }

    /**
     * @return DocBlock
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * Convert element to a PHP string
     *
     * @return string
     */
    public function __toString()
    {
        $string = "{$this->getDocBlock()}";
        if ($string !== '') {
            $string .= "\n";
        }

        $string .= "{$this->getScope()} \${$this->name}";

        if ($this->default !== null) {
            $string .= " = {$this->default}";
        }

        return "{$string};";
    }
}

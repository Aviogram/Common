<?php
namespace Aviogram\Common\PHPClass;

trait ScopeTrait
{
    /**
     * @var array
     */
    protected $scopes = array('public', 'protected', 'private');

    /**
     * @var string
     */
    protected $scope = 'public';

    /**
     * Set the current scope
     *
     * @param  string $scope    (See ScopeInterface::SCOPE_*)
     *
     * @return $this
     *
     * @throws Exception\Scope  When the scope does not exists
     */
    public function setScope($scope)
    {
        if (in_array($scope, $this->scopes) === false) {
            throw Exception\Scope::invalidScope($scope, $this->scopes);
        }

        $this->scope = $scope;

        return $this;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
}

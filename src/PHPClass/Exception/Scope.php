<?php
namespace Aviogram\Common\PHPClass\Exception;

class Scope extends BaseException
{
    /**
     * @param string $scope
     * @param array  $availableScopes
     *
     * @return Scope
     */
    public static function invalidScope($scope, array $availableScopes)
    {
        return new self("Scope '{$scope}' is invalid. Available scopes: " . implode(', ', $availableScopes));
    }
}

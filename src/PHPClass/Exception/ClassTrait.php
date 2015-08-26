<?php
namespace Aviogram\Common\PHPClass\Exception;

class ClassTrait extends BaseException
{
    /**
     * @param  string $knownTrait
     *
     * @return ClassTrait
     */
    public static function knownTraitNotUsedByResolveInsteadOf($knownTrait)
    {
        return new self(
            "When resolving trait conflict by insteadOf argument \$trait or \$insteadOfTrait should have the value '{$knownTrait}'."
        );
    }

    /**
     * @param  string $trait
     *
     * @return ClassTrait
     */
    public static function traitNotFound($trait)
    {
        return new self("Trait '{$trait}' could not be found.");
    }

    /**
     * @param string $trait
     * @param string $method
     *
     * @return ClassTrait
     */
    public static function undefinedMethod($trait, $method)
    {
        return new self("Trait method '{$trait}::{$method}' is not defined.");
    }
}

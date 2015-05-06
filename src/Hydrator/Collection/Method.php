<?php
namespace Aviogram\Common\Hydrator\Collection;

use Aviogram\Common\Hydrator\Entity;

/**
 * @category    Project89109
 * @package     StdLib
 * @subpackage  Hydrator\Entity
 *
 * @method Entity\Method current
 * @method Entity\Method offsetGet(string $index)                 Get value for an offset
 * @method void   offsetSet(string $index, Entity\Method $newval) Set value for an offset
 * @method void   append(Entity\Method $value)                    Append an element
 */
class Method extends \ArrayIterator
{}

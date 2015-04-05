<?php
namespace Aviogram\Common;

use DateTime;
use JsonSerializable;

abstract class AbstractEntity implements JsonSerializable
{
    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        // Grep all the properties
        $data = get_object_vars($this);

        foreach ($data as $index => $value) {
            // Convert DateTime to an common format (ISO8601)
            if ($value instanceof DateTime) {
                $data[$index] = $value->format('c');
            }
        }

        return $data;
    }
}

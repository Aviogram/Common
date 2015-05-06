<?php
namespace Aviogram\Common;

class Base64
{
    /**
     * @param mixed $data
     *
     * @return string | boolean FALSE when the encoding failed
     */
    public static function encodeURLSafe($data)
    {
        $encoded = base64_encode($data);

        if ($encoded === false) {
            return false;
        }

        return str_replace(array('+', '/', '='), array('-', '_', ''), $encoded);
    }

    /**
     * @param string $string
     *
     * @return mixed | boolean FALSE when the decoding failed
     */
    public static function decodeURLSafe($string)
    {
        $remainder = strlen($string) % 4;

        if ($remainder <> 0) {
            $paddingLength = 4 - $remainder;
            $string .= str_repeat('=', $paddingLength);
        }

        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $string));
    }
}

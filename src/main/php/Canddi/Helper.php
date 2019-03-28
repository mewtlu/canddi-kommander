<?php
/**
 * @category
 * @package
 * @copyright  2011-03-14 (c) 2011-12 Campaign and Digital Intelligence
 * @license
 * @author     Tim Langley
 **/

use Canddi\Helper\LoadClasses as NS_LoadClasses;
/**
 *  Useful functions that we don't know where else to put
 **/
class Canddi_Helper
{
    static function toUTF8($mixedValue, $strInputEncoding = 'ASCII,UTF-8,ISO-8859-15,WINDOWS-1252')
    {
        if (is_array($mixedValue)) {
            // okay we need to iterate over it
            // if it's a multidimentional array we can just recursively call it.
            foreach($mixedValue as $strKey => $value) {
                unset($mixedValue[$strKey]);
                $mixedValue[self::toUTF8($strKey)] = self::toUTF8($value);
            }
        }
        if (!is_string($mixedValue)) {
            return $mixedValue;
        }
        $strEncoding = mb_detect_encoding($mixedValue, $strInputEncoding);
        if (false == $strEncoding) {
            //This is a hack - if we can't figure it out
            // then assume it's UTF-8
            $strEncoding = 'UTF-8';
        }
        $mixedValue = mb_convert_encoding($mixedValue, 'UTF-8', $strEncoding);
        return $mixedValue;
    }
}


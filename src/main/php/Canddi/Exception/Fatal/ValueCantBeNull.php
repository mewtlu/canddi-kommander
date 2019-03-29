<?php
/**
 * @category
 * @package
 * @copyright  2011-06-03 (c) 2011-12 Campaign and Digital Intelligence
 * @license
 * @author     Tim Langley
 **/

class Canddi_Exception_Fatal_ValueCantBeNull extends Canddi_Exception_Fatal
{
    const   MESSAGE = 'Value (%s) can\'t be null';

    public function __construct($strType)
    {
        parent::__construct(sprintf(self::MESSAGE, $strType));
    }
}
<?php
/**
 * @category
 * @package
 * @copyright  2011-05-19 (c) 2011-12 Campaign and Digital Intelligence
 * @license
 * @author     Tim Langley
 **/

class Canddi_Helper_Config_MailSettings extends Canddi_Exception
{
    const   MESSAGE = '_initMailSettings - Unknown Type = (%s)';

    public function __construct($strType)
    {
        parent::__construct(sprintf(self::MESSAGE, $strType));
    }
}

<?php
class Canddi_Helper_Config_Exception_FileDoesNotExist extends Canddi_Exception
{
    const MESSAGE = 'File does not exist: (%s)';

    public function __construct($strFile)
    {
        parent::__construct(sprintf(self::MESSAGE, $strFile));
    }
}
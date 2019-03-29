<?php
class Canddi_Helper_Config_Exception_KeyDoesNotExist extends Canddi_Exception
{
    const MESSAGE = 'Key does not exist: (%s), environment: (%s)';

    public function __construct($strKey, $strEnv)
    {
        parent::__construct(sprintf(self::MESSAGE, $strKey, $strEnv));
    }
}
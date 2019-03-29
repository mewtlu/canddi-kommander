<?php
/**
 * Helper to deal with server stuff
 *
 * @package default
 * @author Dan Dart
 **/
class Canddi_Helper_Config
{
    /**
     * This is an array (one per singleton)
     *
     * @author  Tim Langley
     * @var     array
     **/
    private static $_arrConfigInstances = array();

    /**
     * This is the ZendConfigFile
     *
     * @author  Tim Langley
     * @var     Zend_Config
     **/
    protected $_arrConfig;

    public static function getInstance()
    {
        $strClass = get_called_class();
        if (isset(self::$_arrConfigInstances[$strClass])) {
            return self::$_arrConfigInstances[$strClass];
        }

        //Otherwise we should create one
        $newConfigInstance = new $strClass();
        self::$_arrConfigInstances[$strClass] = $newConfigInstance;
        return $newConfigInstance;
    }

    /**
     * Injects a mock helper instance for testing
     *
     * @param   Canddi_Helper_Config_Abstract $mockInstance
     * @return  Canddi_Helper_Config_Abstract
     * @author  Tim Langley
     **/
    public static function inject(Canddi_Helper_Config_Abstract $mockInstance)
    {
        $strClass = get_called_class();
        self::$_arrConfigInstances[$strClass] = $mockInstance;
        return $mockInstance;
    }

    /**
     * Also used for testing - this resets all the instances
     *
     * @return void
     * @author Tim Langley
     **/
    public static function reset()
    {
        self::$_arrConfigInstances = array();
    }

    /**
     * This constructs the actual helper
     *
     * @author Tim Langley
     **/
    private function __construct()
    {
        $strServerFile          = $this->_getConfigFile();
        $objConf       = new Zend_Config_Ini($strServerFile, APPLICATION_ENV);
        $this->_arrConfig       = $objConf->toArray();
    }

    /**
     * This returns the file and path for the config file
     *
     * @return void
     * @author Tim Langley
     **/
    protected function _getConfigFile() {
        return APPLICATION_CONFIG_PATH . '/config.ini';
    }

    /**
     * Recursively gets the next value
     *
     * @param string $value
     * @param string $strKey
     *
     * @return string
     * @throws Canddi_Helper_Config_Exception_KeyDoesNotExist
     * @author Dan Dart
     **/
    protected function _getValue(Array $arrProperties, $strKey, $mixedDefault = null)
    {
        $arrLocal   = $this->_arrConfig;
        foreach($arrProperties as $strPropertyKey) {
            if(!isset($arrLocal[$strPropertyKey])) {
                if(!is_null($mixedDefault)){
                    return $mixedDefault;
                }

                throw new Canddi_Helper_Config_Exception_KeyDoesNotExist(
                    $strKey,
                    APPLICATION_ENV
                );
            }

            $arrLocal = $arrLocal[$strPropertyKey];
        }

        if(!is_null($arrLocal) || '' != $arrLocal || [] != $arrLocal) {
            return $arrLocal;
        }

        if(!is_null($mixedDefault)){
            return $mixedDefault;
        }

        throw new Canddi_Helper_Config_Exception_KeyDoesNotExist(
            $strKey,
            APPLICATION_ENV
        );
    }

    public function getGithubUsername()
    {
        return $this->_getValue(
            ["GITHUB_USERNAME"],
            "GITHUB_USERNAME"
        );
    }

    public function getGithubPAT()
    {
        return $this->_getValue(
            ["GITHUB_PAT"],
            "GITHUB_PAT"
        );
    }
}

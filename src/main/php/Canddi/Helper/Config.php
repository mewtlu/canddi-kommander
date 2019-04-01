<?php
/**
 * Helper to deal with server stuff
 *
 * @package default
 * @author Dan Dart
 **/
class Canddi_Helper_Config
{
    use Canddi_Interface_Singleton;

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

    public function getOrganisation()
    {
        return $this->_getValue(
            ["GITHUB_ORGANISATION"],
            "GITHUB_ORGANISATION"
        );
    }
}

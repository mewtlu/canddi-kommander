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

    const STATIC_DIRECTORY = 'static';
    const GITHUB_CODEOWNERS_FILEPATH = self::STATIC_DIRECTORY . "/CODEOWNERS";

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

    /**
     * Returns an array of the contents of all files in the static folder
     * @return array - Array of strings of file contents
     */
    public function getStaticFiles()
    {
        if (! file_exists(self::GITHUB_CODEOWNERS_FILEPATH)) {
            throw new Canddi_Helper_Config_Exception_FileDoesNotExist(
                self::GITHUB_CODEOWNERS_FILEPATH
            );
        }

        $arrStaticFileContents = [];
        $arrStaticFiles = array_diff(
            scandir(self::STATIC_DIRECTORY),
            ['.', '..']
        );

        foreach ($arrStaticFiles as $strFilename) {
            echo $strFilename;
            $strFilepath = self::STATIC_DIRECTORY . "/$strFilename";
            $streamFile = fopen($strFilepath, 'r');
            $arrStaticFileContents[$strFilename] = fread($streamFile, filesize($strFilepath));
        }

        return $arrStaticFileContents;
    }
}

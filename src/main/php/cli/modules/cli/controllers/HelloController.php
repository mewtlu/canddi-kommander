<?php
/*
 *    ./scripts/callPHP.sh -a cli.hello.world.get -p "name=Logan"
 */
class cli_HelloController extends Cli_Abstract
{
    /**
     * Returns an array of the contexts that this controller supports
     * This looks like:
     *  'text' => array(settings)
     *  'json' => array() -> use default settings
     *  'newContext' => array(settings)
     *  In this instance the settings come from Zend_Controller_Action_Helper_ContextSwitch
     *
     * @return array
     * @author Tim Langley
     **/
    protected function _getSupportedContexts()
    {
        return [
            self::CONTEXT_JSONPRETTY => []
        ];
    }
    /**
     * Returns a joyous greeting
     *
     * Parameters:
     *   name
     *
     * @return void
     * @author Logan White
     **/
    public function worldAction_GET()
    {
        $strName                  = $this->_request->getParam('name', null);
        if (empty($strName)) {
            throw new Canddi_Exception_Fatal_ValueCantBeNull('name');
        }

        $config = \Canddi_Helper_Config::getInstance();
        $this->view->assign([
            'Message'           => "Hello $strName",
            'GithubUsername'    => $config->getGithubUsername(),
            'GithubPAT'         => $config->getGithubPAT()
        ]);
    }
}


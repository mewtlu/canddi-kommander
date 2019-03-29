<?php
class cli_GithubController
extends Cli_Abstract
{
    use Canddi_Interface_Singleton;

    function cliInit() {
        $this->github = \Canddi_Helper_Github::getInstance();
    }

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
     * Creates a repository and initalizes it with required config
     *
     * Example usage: ./scripts/callPHP.sh -a cli.github.create.post -p "repository=canddi-kommander"
     *
     * Parameters:
     *   repository
     *
     * @return void
     * @author Luke Roberts
     **/
    public function createAction_POST()
    {
        $strRepo = $this->_request->getParam('repository', null);
        if (empty($strRepo)) {
            throw new Canddi_Exception_Fatal_ValueCantBeNull('repository');
        }

        $this->view->assign([
            'Repository' => "$strRepo",
            'Username' => $this->github->getUsername(),
            'Access Token' => $this->github->getAccessToken()
        ]);
    }

    /**
     * Updates an existing repository with required config
     *
     * Example usage: ./scripts/callPHP.sh -a cli.github.update.post -p "repository=canddi-kommander"
     *
     * Parameters:
     *   repository
     *
     * @return void
     * @author Luke Roberts
     **/
    public function updateAction_POST()
    {
        $strRepo = $this->_request->getParam('repository', null);
        if (empty($strRepo)) {
            throw new Canddi_Exception_Fatal_ValueCantBeNull('repository');
        }

        $this->view->assign([
            'Update repository' => "$strRepo",
            'Username' => $this->github->getUsername(),
            'Access Token' => $this->github->getAccessToken()
        ]);
    }
}


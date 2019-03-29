<?php
class cli_GithubController extends Cli_Abstract
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
}


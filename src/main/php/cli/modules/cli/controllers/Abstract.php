<?php
/**
 * Cli_Abstract
 * This provides several REST specalist services to extend Zend_Controller_Action
 *
 * @package default
 * @author Tim Langley
 * @author Dan Dart
 **/
abstract class Cli_Abstract extends Zend_Controller_Action
{
    const CONTEXT_JSON          = 'json';
    const CONTEXT_JSONPRETTY    = 'jsonpretty';
    const CONTEXT_QUIET         = 'quiet';
    const CONTEXT_TEXT          = 'text';

    /**
     * cliInit()
     * Override this in child classes as per the usual init()
     *
     * @return void
     * @author Tim Langley
     **/
    public function cliInit()
    {
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
    abstract protected function _getSupportedContexts();

    /**
     * This returns the default contexts
     *
     * @return array
     * @author Tim Langley
     **/
    private function _getDefaultContexts()
    {
        return [
            self::CONTEXT_JSON => [
                'suffix' => 'json',
                'disableLayout' => true,
                'callbacks' => [
                    'init' => 'initJsonContext',
                    'post' => 'postJsonContext'
                ]
            ],
            self::CONTEXT_JSONPRETTY => [
                'suffix' => 'json',
                'disableLayout' => true,
                'callbacks' => [
                    'init' => 'initJsonContext',
                    'post' => 'postJsonPrettyContext'
                ]
            ],
            self::CONTEXT_QUIET => [
                'suffix' => '',
                'disableLayout' => true,
                'callbacks' => [
                    // This is a cheat: it disables the view.
                    'init' => 'initJsonContext'
                ]
            ],
            self::CONTEXT_TEXT => [
                'suffix' => ''
            ]
        ];
    }

    /**
     * init()
     * This class extends Zend_Controller_Action::init() with a final method
     * SO that this function ALWAYS gets called
     *
     * This class provides a function called restInit() which can be over-ridden in each child class
     *
     * @return void
     * @author Tim Langley
     **/
    public final function init()
    {
        // This is the call to restInit which can be over-ridden in each child
        $this->cliInit();

        $arrDefaultContexts = $this->_getDefaultContexts();

        //Here we program to make sure that the contexts are all configured properly
        $arrSupportedContexts = $this->_getSupportedContexts();
        foreach ($arrSupportedContexts AS $contextKey => $arrValues) {
            if (empty($arrValues) || !is_array($arrValues)) {
                if (!isset($arrDefaultContexts[$contextKey])) {
                    throw new Exception("Context $contextKey is not configured properly");
                }
                $arrSupportedContexts[$contextKey] = $arrDefaultContexts[$contextKey];
            }
        }
        //TODO HERE - need to confirm that there is at least one context
        //TODO HERE - need to confirm that at least one contextKey

        $arrContextKeys = array_keys($arrSupportedContexts);

        // We remove ALL contexts before adding our own. This makes sure we ONLY use the contexts we supply here.
        $this
            ->_helper
            ->cliContextSwitch()
            ->clearContexts()
            ->addContexts($arrSupportedContexts)
            ->clearActionContexts()
            ->addGlobalContext($arrContextKeys)
            ->setDefaultContext($arrContextKeys[0])
            ->initContext();
    }

    /**
     * Post-dispatch routines
     *
     * Called after action method execution. If using class with
     * {@link Zend_Controller_Front}, it may modify the
     * {@link $_request Request object} and reset its dispatched flag in order
     * to process an additional action.
     *
     * Common usages for postDispatch() include rendering content in a sitewide
     * template, link url correction, setting headers, etc.
     *
     * @return void
     */
    public function postDispatch()
    {
    }
    /**
     * Should we sent the buffered output
     * (default = yes we should)
     *
     * @param   bBuffer
     **/
    protected function _sendBufferOutput()
    {
        $bBuffer    = $this->_request->getParam('bBuffer', false);

        if(false === $bBuffer) {
            $this->_helper->notifyPostDispatch();
            $this->getResponse()->sendResponse();
            $this->getResponse()->clearBody();
            $this->view->clearVars();
        }
    }
}

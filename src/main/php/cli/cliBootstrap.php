<?php
/**
 * @category
 * @package
 * @copyright  2010-12-16, Campaign and Digital Intelligence Ltd
 * @license
 * @author     Tim Langley
 **/
class cliBootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    const   LOG_TITLE = 'cli';

    protected function _initPlugins()
    {
        $this->bootstrap('frontController');
        $cliContext = new cliCommon_Controller_Action_Helper_CliContextSwitch();
        Zend_Controller_Action_HelperBroker::addHelper($cliContext);
    }

    protected function _bootstrap($resource = null)
    {
        try {
                parent::_bootstrap($resource);
        } catch (Exception $e) {
            $this->__handleErrors($e);
        }
    }

    public function run()
    {
        try {
                parent::run();
        } catch (Exception $e) {
            $this->__handleErrors($e);
        }
    }

    protected function __handleErrors(Exception $e)
    {
        echo 'A fatal error has occurred in Canddi - Development team have been notified' . chr(10);
        echo 'Error Message = ' . $e->getMessage() . chr(10) . chr(10);
    }
}


<?php
/**
 * @category
 * @package
 * @copyright  2010-12-16, Campaign and Digital Intelligence Ltd
 * @license
 * @author     Tim Langley
 **/

class CliControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    public $application;
    /**
     * This function uses reflection to get the value of a Protected or Private attribute
     * This is useful for us to test the internal data structures
     *
     * @param string $obj
     * @param string $attr
     * @return mixed - the value
     * @author Dan Dart
     **/
    protected function _getProtAttr($obj, $attr)
    {
        $reflection = new ReflectionClass($obj);
        $prop = $reflection->getProperty($attr);
        $prop->setAccessible(true);
        return $prop->getValue($obj);
    }
    public function setUp()
    {
        $this->application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/cli/config/application.ini');
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }
    /**
     * This function sets a static value
     *
     * @param string $obj
     * @param string $attr
     * @param string $value
     *
     * @author Tim Langley
     **/
    protected function _setProtAttr($obj, $attr, $value)
    {
        $reflectedClass     = new ReflectionClass($obj);
        $reflectedProperty  = $reflectedClass->getProperty($attr);
        $reflectedProperty->setAccessible(true);
        $reflectedProperty->setValue($value);
    }
    /**
     * Use reflection again to invoke a protected or private method
     * Takes an optional arg
     *
     * @param string $obj
     * @param string $method
     * @param string $arg (optional)
     * @return the method's return value
     * @author Tim Langley
     **/
    protected function _invokeProtMethod($obj, $method, $arg = null)
    {
        $reflection = new ReflectionClass($obj);
        $refMethod = $reflection->getMethod($method);
        $refMethod->setAccessible(true);
        return $refMethod->invoke($obj, $arg);
    }
    public function tearDown()
    {
        Mockery::close();
        Zend_Registry::_unsetInstance();

        $this->reset();
        $this->_resetPlaceholders();
        $this->resetRequest()
            ->resetResponse();
        $this->request->setPost(array());
        $this->request->setQuery(array());

        $this->_tearDown();
    }
    //Override if you wish
    public function _tearDown()
    {

    }

    public function appBootstrap()
    {
        $this->application->bootstrap();
    }

    /**
     *    $strControllerAction should be "module.controller.action [optional .verb]"
     **/
    public function dispatch($strControllerAction = null, $bThrowExceptions = true)
    {

        // redirector should not exit
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->setExit(false);

        // json helper should not exit
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;

        /**
         * @purpose:     These four lines are the new clever ones
         *                They configure the CLI environment
         **/
        $arrActionParams= explode('.', $strControllerAction);

        if(2 === count($arrActionParams)) {
            $module     = "listeners";
            $controller = $arrActionParams[0];
            $action     = $arrActionParams[1];
            $strVerb    = cliCommon_Controller_Dispatcher_Cli::DEFAULT_METHOD;
        } else {
            $module     = $arrActionParams[0];
            $controller = $arrActionParams[1];
            $action     = $arrActionParams[2];
            $strVerb    = isset($arrActionParams[3])?$arrActionParams[3]:cliCommon_Controller_Dispatcher_Cli::DEFAULT_METHOD;
        }

        $arrParams      = $this->getRequest()->getParams();
        $arrParams['method'] = $strVerb;

        $this->_request     = new Zend_Controller_Request_Simple($action, $controller, $module, $arrParams);
        $this->_response    = new Zend_Controller_Response_Cli();
        $this->frontController->setRouter(new cliCommon_Controller_Router_Cli());

        $dispatcher             = new cliCommon_Controller_Dispatcher_Cli([
            'prefixDefaultModule' => true,
            'useDefaultControllerAlways' => true
        ]);
        $dispatcher->setDefaultModule('listeners');
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/modules/cli/controllers',        'cli');
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/modules/listeners/controllers',  'listeners');
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/modules/schedule/controllers',   'schedule');

        $this->frontController->setDispatcher($dispatcher);

        $request                = $this->getRequest();

        $this->frontController->setRequest($request)
            ->setResponse($this->getResponse())
            ->throwExceptions($bThrowExceptions)
            ->returnResponse(true);

        if ($this->bootstrap instanceof Zend_Application) {
            $this->bootstrap->run();
        } else {
            $this->frontController->dispatch();
        }
    }
}

<?php
class cli_testoneController extends Zend_Controller_Action
{
    public function testAction()
    {
    }
}
class cli_testtwoController extends Cli_Abstract {
    protected function _getSupportedContexts() { return []; }
}
class cliCommon_Controller_Dispatcher_CliTest
    extends cliControllerTestCase
{
    public function testIsCliController_ClassNameDoesntExist()
    {
        $mockRequest = Mockery::mock('Zend_Controller_Request_Abstract')
            ->shouldReceive('getControllerName')
            ->andReturn('')
            ->once()
            ->mock();
        $dispatcher = new cliCommon_Controller_Dispatcher_Cli(['prefixDefaultModule' => true]);
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/cli/controllers', 'cli');
        $this->assertFalse($dispatcher->isCliController($mockRequest));
    }

    public function testIsCliController_ClassDoesntExist()
    {
        $mockRequest = Mockery::mock('Zend_Controller_Request_Abstract')
            ->shouldReceive('getControllerName')
            ->andReturn('test')
            ->once()
            ->shouldReceive('getModuleName')
            ->andReturn('cli')
            ->once()
            ->mock();
        $dispatcher = new cliCommon_Controller_Dispatcher_Cli(['prefixDefaultModule' => true]);
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/cli/controllers', 'cli');
        $this->assertFalse($dispatcher->isCliController($mockRequest));
    }

    public function testIsCliController_DoesntExtendFromRightClass()
    {
        $mockRequest = Mockery::mock('Zend_Controller_Request_Abstract')
            ->shouldReceive('getControllerName')
            ->andReturn('testone')
            ->once()
            ->shouldReceive('getModuleName')
            ->andReturn('cli')
            ->once()    
            ->mock();
        $dispatcher = new cliCommon_Controller_Dispatcher_Cli(['prefixDefaultModule' => true]);
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/cli/controllers', 'cli');
        $this->assertFalse($dispatcher->isCliController($mockRequest));
    }

    public function testIsCliController_True()
    {
        $mockRequest = Mockery::mock('Zend_Controller_Request_Abstract')
            ->shouldReceive('getControllerName')
            ->andReturn('testtwo')
            ->once()
            ->shouldReceive('getModuleName')
            ->andReturn('cli')
            ->once()
            ->mock();
        $dispatcher = new cliCommon_Controller_Dispatcher_Cli(['prefixDefaultModule' => true]);
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/cli/controllers', 'cli');
        $this->assertTrue($dispatcher->isCliController($mockRequest));
    }

    public function testGetActionMethod_Empty()
    {
        $mockRequest = Mockery::mock('Zend_Controller_Request_Abstract')
            ->shouldReceive('getActionName')
            ->andReturn('')
            ->once()
            ->shouldReceive('setActionName')
            ->with('index')
            ->once()
            ->shouldReceive('getControllerName')
            ->andReturn('test')
            ->once()
            ->shouldReceive('getModuleName')
            ->andReturn('cli')
            ->once()
            ->mock();
        $dispatcher = new cliCommon_Controller_Dispatcher_Cli(['prefixDefaultModule' => true]);
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/cli/controllers', 'cli');
        $this->assertEquals('indexAction', $dispatcher->getActionMethod($mockRequest));
    }

    public function testGetActionMethod_Standard()
    {
        $mockRequest = Mockery::mock('Zend_Controller_Request_Abstract')
            ->shouldReceive('getActionName')
            ->andReturn('test')
            ->once()
            ->shouldReceive('getControllerName')
            ->andReturn('testone')
            ->once()
            ->shouldReceive('getModuleName')
            ->andReturn('cli')
            ->once() 
            ->mock();
        $dispatcher = new cliCommon_Controller_Dispatcher_Cli(['prefixDefaultModule' => true]);
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/cli/controllers', 'cli');
        $this->assertEquals('testAction', $dispatcher->getActionMethod($mockRequest));
    }

    public function testGetActionMethod_CliAction()
    {
        $mockRequest = Mockery::mock('Zend_Controller_Request_Abstract')
            ->shouldReceive('getActionName')
            ->andReturn('test')
            ->once()
            ->shouldReceive('getControllerName')
            ->andReturn('testtwo')
            ->once()
            ->shouldReceive('getModuleName')
            ->andReturn('cli')
            ->once()
            ->shouldReceive('getParam')
            ->with('method')
            ->andReturn('method')
            ->once()
            ->mock();
        $dispatcher = new cliCommon_Controller_Dispatcher_Cli(['prefixDefaultModule' => true]);
        $dispatcher->addControllerDirectory(APPLICATION_PATH.'/cli/cli/controllers', 'cli');
        $this->assertEquals('testAction_METHOD', $dispatcher->getActionMethod($mockRequest));
    }
}
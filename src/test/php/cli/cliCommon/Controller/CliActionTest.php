<?php
class testAction extends Cli_Abstract
{
    public function _getSupportedContexts()
    {
        return [
            Cli_Abstract::CONTEXT_TEXT => []
        ];
    }
}
class Cli_AbstractTest extends cliControllerTestCase
{
    public function testInit()
    {
        $mockRequest    = Mockery::mock('Zend_Controller_Request_Abstract')
            ->shouldReceive('getActionName')
            ->andReturn('test')
            ->once()
            ->shouldReceive('getParam')
            ->with('accept')
            ->andReturn('text')
            ->once()
            ->mock();
        $mockResponse   = Mockery::mock('Zend_Controller_Response_Abstract');
        // __construct() calls init()
        $action         = new testAction($mockRequest, $mockResponse);
    }
}
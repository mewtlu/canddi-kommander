<?php
class cli_HelloControllerTest
    extends CliControllerTestCase
{
    public function testHelloAction_NoParameter()
    {
        $this->setExpectedException("Canddi_Exception_Fatal_ValueCantBeNull");
        $this->dispatch('cli.hello.world.get');
    }

    public function testWorldAction()
    {
        $strName      = "Logan";
        $this->_request->setParams([
            'name'    => $strName
        ]);

        $arrExpected = [
            "Message" => "Hello $strName"
        ];

        $this->dispatch('cli.hello.world.get');
        
        $arrResponse    = json_decode($this->getResponse()->getBody(), true);
        $this->assertEquals($arrExpected, $arrResponse);
    }
}

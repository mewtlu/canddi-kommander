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
        $config = \Canddi_Helper_Config::getInstance();
        $strGithubUsername = $config->getGithubUsername();
        $strGithubPAT = $config->getGithubPAT();
        $this->_request->setParams([
            'name'    => $strName
        ]);

        $arrExpected = [
            "Message"           => "Hello $strName",
            'GithubUsername'    => $strGithubUsername,
            'GithubPAT'         => $strGithubPAT
        ];

        $this->dispatch('cli.hello.world.get');
        
        $arrResponse    = json_decode($this->getResponse()->getBody(), true);
        $this->assertEquals($arrExpected, $arrResponse);
    }
}

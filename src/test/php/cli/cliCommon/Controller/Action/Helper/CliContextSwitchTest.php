<?php
class cliCommon_Controller_Action_Helper_CliContextSwitchTest
    extends CliControllerTestCase
{
    public function testDefaultGetters()
    {
        $cs = new cliCommon_Controller_Action_Helper_CliContextSwitch();
        $this->assertTrue(is_array($cs->getActionContexts()));
        $this->assertTrue($cs->hasActionContext('a','b'));
    }

    public function testAddActionContext()
    {
        $this->setExpectedException('Zend_Controller_Action_Exception');
        $cs = new cliCommon_Controller_Action_Helper_CliContextSwitch();
        $cs->addActionContext('a','b');
    }

    public function testAddContext_NoDisable()
    {
        $cs = new cliCommon_Controller_Action_Helper_CliContextSwitch();
        $strContext = 'context';
        $arrSpec    = [];
        $this->asserTEquals($cs, $cs->addContext($strContext, $arrSpec));
    }

    public function testAddContext_DisableLayout()
    {
        $cs = new cliCommon_Controller_Action_Helper_CliContextSwitch();
        $strContext = 'context';
        $arrSpec    = ['disableLayout' => true];
        $this->asserTEquals($cs, $cs->addContext($strContext, $arrSpec));
    }
}
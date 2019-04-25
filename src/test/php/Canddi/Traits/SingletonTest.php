<?php
/**
 * @category
 * @package
 * @copyright  2011-06-05 (c) 2011 Campaign and Digital Intelligence (http://canddi.com)
 * @license
 * @author     Tim Langley
 **/

use Canddi\Kommander\TestCase;

class SingletonTestClass {
    use Canddi_Trait_Singleton;


}

class SingletonTest extends TestCase
{
    public function testGetInstance()
    {
        $arrExpectedConfigInstances = [
            'SingletonTestClass' => SingletonTestClass::getInstance(),
        ];

        $this->assertTrue(
            SingletonTestClass::getInstance() instanceof SingletonTestClass
        );
        $this->assertEquals(
            $arrExpectedConfigInstances,
            $this->_getProtAttr('SingletonTestClass', '_arrConfigInstances')
        );
    }

    public function testInject()
    {
        $mockSingletonTestClass = \Mockery::mock('SingletonTestClass');
        SingletonTestClass::inject($mockSingletonTestClass);

        $this->assertEquals(
            [
                'SingletonTestClass' => $mockSingletonTestClass
            ],
            $this->_getProtAttr('SingletonTestClass', '_arrConfigInstances')
        );
    }

    public function testReset()
    {
        SingletonTestClass::getInstance();
        SingletonTestClass::reset();

        $this->assertEquals(
            [],
            $this->_getProtAttr('SingletonTestClass', '_arrConfigInstances')
        );
    }
}

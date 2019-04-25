<?php
/**
 * @category
 * @package
 * @copyright  2011-06-05 (c) 2011 Campaign and Digital Intelligence (http://canddi.com)
 * @license
 * @author     Tim Langley
 **/

use Canddi\Kommander\TestCase;

class Canddi_HelperTest extends TestCase
{
    public function testToUTF8Pound()
    {
        $strFrom = "\xa3";
        $strTo = "\xc2\xa3";
        $this->assertEquals($strTo, Canddi_Helper::toUTF8($strFrom));
    }
    public function testToUTF8()
    {
        // First we'll test normal string.
        $strExample = "kaltx\xc3\xac";
        $strUtf = 'kaltxì';

        $strResult = Canddi_Helper::toUTF8($strExample);

        $this->assertEquals($strResult, $strUtf);

        // now test multidimentinal arrays
        $arrTest = array(
            "kaltx\xc3\xac" => array(
                "Oe t\xc3\xa4txaw" => "kosman ma tsmuk\xc3\xa9!"
                ),
                "Ngey\xc3\xa4 key" => "lu h\xc3\xacyik srak?"
        );

        $arrExpected = array(
            'Kaltxì' => array(
                'Oe tätxaw' => 'kosman ma tsmuké!'
            ),
            'Ngeyä key' => 'lu hìyik srak?'
        );

        $arrResult = Canddi_Helper::toUTF8($arrExpected);

        $this->assertEquals($arrExpected, $arrResult);
    }
}

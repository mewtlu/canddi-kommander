<?php

namespace Canddi\Kommander\Helper\Config;
use Canddi\Kommander\TestCase;

class Canddi_Helper_ConfigTest
    extends TestCase
{
    public function testConstructInstance()
    {
        $modelHelperConfig = \Canddi_Helper_Config::getInstance();

        $this->assertTrue($modelHelperConfig instanceof \Canddi_Helper_Config);

        $strConfigFilePath = APPLICATION_CONFIG_PATH . '/config.ini';

        $arrExpectedConfig = [
            'GITHUB_USERNAME' => 'Unit-Test-USER',
            'GITHUB_PAT' => 'Unit-Test-PAT',
            'GITHUB_ORGANISATION' => 'Unit-Test-ORG',
        ];
        $arrReceivedConfig = $this->_getProtAttr($modelHelperConfig, '_arrConfig');
        $this->assertEquals(
            $arrExpectedConfig,
            $arrReceivedConfig
        );
    }

    public function testConstructSingleton()
    {
        $modelHelperConfig1 = \Canddi_Helper_Config::getInstance();
        $modelHelperConfig2 = \Canddi_Helper_Config::getInstance();

        $this->assertTrue($modelHelperConfig1 instanceof $modelHelperConfig2);
    }

    public function testGettersSetters()
    {
        $modelHelperConfig = \Canddi_Helper_Config::getInstance();

        $strGithubUsername = 'Unit-Test-USER';
        $strGithubPAT = 'Unit-Test-PAT';
        $strGithubOrganisation = 'Unit-Test-ORG';
        $arrStaticFiles = [
            'CODEOWNERS' => '* @Deep-Web-Technologies/canmergetodev',
            'README.md' => "# Example README\n\ntest example\n\n* 1\n* 2",
        ];

        $this->assertEquals(
            $strGithubUsername,
            $modelHelperConfig->getGithubUsername()
        );
        $this->assertEquals(
            $strGithubPAT,
            $modelHelperConfig->getGithubPAT()
        );
        $this->assertEquals(
            $strGithubOrganisation,
            $modelHelperConfig->getOrganisation()
        );
        $this->assertEquals(
            $arrStaticFiles,
            $modelHelperConfig->getStaticFiles()
        );
    }
}

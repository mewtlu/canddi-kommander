<?php

namespace Canddi\Kommander\Helper\Config;
use Canddi\Kommander\TestCase;
use GuzzleHttp\Client as GuzzleClient;

class GithubTest
    extends TestCase
{
    public function testConstructInstance()
    {
        $modelHelperConfig = \Canddi_Helper_Github::getInstance();

        $this->assertTrue($modelHelperConfig instanceof \Canddi_Helper_Github);
    }

    public function testConstructSingleton()
    {
        $modelHelperConfig1 = \Canddi_Helper_Github::getInstance();
        $modelHelperConfig2 = \Canddi_Helper_Github::getInstance();

        $this->assertTrue($modelHelperConfig1 instanceof $modelHelperConfig2);
    }

    public function testConstructSetters()
    {
        $modelHelperGithub = \Canddi_Helper_Github::getInstance();

        $strGithubUsername = 'Unit-Test-USER';
        $strGithubPAT = 'Unit-Test-PAT';
        $strGithubOrganisation = 'Unit-Test-ORG';
        $arrStaticFiles = [
            'CODEOWNERS' => '* @Deep-Web-Technologies/canmergetodev',
            'README.md' => "# Example README\n\ntest example\n\n* 1\n* 2",
        ];

        $this->assertEquals(
            $strGithubUsername,
            $modelHelperGithub->getUsername()
        );
        $this->assertEquals(
            $strGithubPAT,
            $modelHelperGithub->getAccessToken()
        );
        $this->assertEquals(
            $strGithubOrganisation,
            $modelHelperGithub->getOrganisation()
        );
        $this->assertEquals(
            $arrStaticFiles,
            $modelHelperGithub->getStaticFiles()
        );
        $this->assertTrue(
            $modelHelperGithub->getGuzzleConnection() instanceOf GuzzleClient
        );
    }
}

<?php

namespace Canddi\Kommander\Helper;
use Canddi\Kommander\TestCase;
use GuzzleHttp\Client as GuzzleClient;

class GuzzleTest
    extends TestCase
{
    public function testConstructInstance()
    {
        $strBaseUri = 'example.com';
        $strAccessToken = 'exampleAccessToken';

        $modelHelperGuzzle = \Canddi_GuzzleFactory::build($strBaseUri, $strAccessToken);

        $this->assertTrue(
            $modelHelperGuzzle instanceOf GuzzleClient
        );
    }
}
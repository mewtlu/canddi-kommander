<?php

class Canddi_GuzzleFactory
{
  protected static $_guzzleConnection = null;
  protected function _call($strUrl, $strMethod, $jsonBody = null)

  public static function build($strBaseUri, $strAccessToken) {
    if(!self::$_guzzleConnection) {
      $arrDefaults = [
          'base_uri'            => $strBaseUri,
          'connect_timeout'     => 5,
          'timeout'             => 5,
          'headers'             => [
              'Accept'          => 'application/json',
              'Accept-Encoding' => 'gzip, deflate',
              'Authorization'   => "token $strAccessToken"
          ],
          'verify'              => false
      ];

      return self::$_guzzleConnection = new Client($arrDefaults);
    }
  }
}
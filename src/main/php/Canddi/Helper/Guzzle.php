<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Canddi_GuzzleFactory
{
  protected static $_guzzleConnection = null;

  public static function build($strBaseUri, $strAccessToken) {
    if(!self::$_guzzleConnection) {
      $arrDefaults = [
        'headers' => [
          'Authorization' => "token $strAccessToken",
          'Content-Type' => 'application/json',
          'Accept' => 'application/json'
        ]
      ];

      return self::$_guzzleConnection = new Client($arrDefaults);
    }
    return self::$_guzzleConnection;
  }
}
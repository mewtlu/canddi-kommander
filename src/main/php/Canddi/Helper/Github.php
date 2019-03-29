<?php
/**
 * Helper for Github update functions
 */
class Canddi_Helper_Github
{
  use Canddi_Interface_Singleton;

  private function __construct (
  ) {
    $modelHelperConfig = \Canddi_Helper_Config::getInstance();
    $this->setConfig($modelHelperConfig);

    $this->setUsername($this->config->getGithubUsername());
    $this->setAccessToken($this->config->getGithubPAT());
  }

  public function setConfig($modelHelperConfig) {
    $this->config = $modelHelperConfig;
  }

  public function setUsername($strUsername) {
    $this->username = $strUsername;
  }

  public function getUsername() {
    return $this->username;
  }

  public function setAccessToken($strAccessToken) {
    $this->access_token = $strAccessToken;
  }

  public function getAccessToken() {
    return $this->access_token;
  }
}
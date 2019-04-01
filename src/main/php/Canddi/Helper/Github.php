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

    $this->setUsername($this->getConfig()->getGithubUsername());
    $this->setAccessToken($this->getConfig()->getGithubPAT());
    $this->setOrganisation($this->getConfig()->getOrganisation());

    $this->guzzleConnection = \Canddi_GuzzleFactory::build(
      self::GITHUB_ROOT_URL,
      $this->getAccessToken()
    );
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

  public function setOrganisation($strOrganisation) {
    $this->access_token = $strOrganisation;
  }

  public function getOrganisation() {
    return $this->access_token;
  }

  private function createNewRepository($strRepository) {
    /**
     * In here we'll make the call to create a new repo of name $strRepository
     **/
  }

  private function updateSettings($strRepository) {
    /**
     * In here we'll run:
     *  $this->createBranch($strRepository, self::DEFAULT_BRANCH);
     *  $this->setDefaultBranch($strRepository, self::DEFAULT_BRANCH);
     *  $this->setCodeOwners($strRepository, self::CODEOWNERS);
     *  $this->setBranchProtection($strRepository, self::PROTECTION_RULES);
     **/
  }

  public function createRepository($strRepository) {
    $this->createNewRepository($strRepository);

    $this->updateSettings($strRepository);
  }

  public function updateRepository($strRepository) {
    $this->updateSettings($strRepository);
  }
}
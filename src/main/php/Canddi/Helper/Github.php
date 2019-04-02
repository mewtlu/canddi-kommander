<?php
/**
 * Helper for Github update functions
 **/
use GuzzleHttp\Exception\RequestException;

class Canddi_Helper_Github
{
  use Canddi_Interface_Singleton;

  const GITHUB_ROOT_URL = 'https://api.github.com/';
  ) {

  private function __construct () {
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

  private function callApi($strMethod, $strEndpoint, $arrBody = []) {
    return $this->guzzleConnection->request(
      $strMethod,
      self::GITHUB_ROOT_URL . "$strEndpoint",
      [
        'json' => $arrBody
      ]
    );
  }

  /**
   * createNewRepository
   * @param  [string] $strRepository - Name of repository to create
   * @return [array] If successful returns array of repository data, else error
   */
  private function createNewRepository($strRepository) {
    $strOrganisation = $this->getOrganisation();

    try {
      $response = $this->callApi(
        'POST',
        "orgs/$strOrganisation/repos",
        [
          'name' => $strRepository
        ]
      );

      if ($response->getStatusCode() === 201) {
        return JSON_encode($response->getBody());
      } else {
        echo "Unknown error:", var_export($response);
      }
    } catch (RequestException $exception) {
      /* For some reason the call failed, return the error */
      $response = $exception->getResponse();
      return [
        'code' => $response->getStatusCode(),
        'phrase' => $response->getReasonPhrase(),
        'response' => JSON_decode($response->getBody())
      ];
    }
  }

  public function createRepository($strRepository) {
    $arrRepositoryInfo = $this->createNewRepository($strRepository);

    $this->updateSettings($strRepository);

    return [
      'repository_info' => $arrRepositoryInfo,
    ];
  }

  public function getConfig() {
    return $this->config;
  }

  public function getUsername() {
    return $this->username;
  }

  public function getAccessToken() {
    return $this->access_token;
  }

  public function getOrganisation() {
    return $this->organisation;
  }

  public function setConfig($modelHelperConfig) {
    $this->config = $modelHelperConfig;
  }

  public function setUsername($strUsername) {
    $this->username = $strUsername;
  }

  public function setAccessToken($strAccessToken) {
    $this->access_token = $strAccessToken;
  }

  public function setOrganisation($strOrganisation) {
    $this->organisation = $strOrganisation;
  }

  public function updateRepository($strRepository) {
    $this->updateSettings($strRepository);
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
}
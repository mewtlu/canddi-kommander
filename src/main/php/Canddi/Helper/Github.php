<?php
/**
 * Helper for Github update functions
 **/
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

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

  public function setConfig($modelHelperConfig) {
    $this->config = $modelHelperConfig;
  }

  public function getConfig() {
    return $this->config;
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
    $this->organisation = $strOrganisation;
  }

  public function getOrganisation() {
    return $this->organisation;
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

  /**
   * If doesn't exist, create a branch (this is usually develop)
   * @param  [str] $strRepository - Name of repository
   * @param  [str] $strBranchName - Name of branch to create
   * @return void
   */
  private function createBranch($strRepository, $strBranchName) {
    $strOrganisation = $this->getOrganisation();
    $strCommitHash = '';

    try {
      $this->callApi(
        'GET',
        "repos/$strOrganisation/$strRepository/branches/$strBranchName"
      );
      // if this doesn't error, the branch exists, so we can exit.
      return true;
    } catch (ClientException $exception) {
      // do nothing here, just continue
    }

    // get the hash
    try {
      $getHashResponse = $this->callApi(
        'GET',
        "repos/$strOrganisation/$strRepository/git/refs/heads"
      );
    } catch (ClientException $exception) {
      $response = $exception->getResponse();
      return [
        'code' => $response->getStatusCode(),
        'phrase' => $response->getReasonPhrase(),
        'response' => JSON_decode($response->getBody())
      ];
    }

    // create the branch
    try {
      $getHashResponse = $this->callApi(
        'POST',
        "self::GITHUB_ROOT_URL/repos/$strOrganisation/$strRepository/git/refs",
        [
          "ref" => "refs/heads/$strBranchName",
          "sha" => "$strCommitHash"
        ]
      );
    } catch (ClientException $exception) {
      $response = $exception->getResponse();
      return [
        'code' => $response->getStatusCode(),
        'phrase' => $response->getReasonPhrase(),
        'response' => JSON_decode($response->getBody())
      ];
    }
  }

  /**
   * Updates a repository's settings to match the required settings
   * @param  [string] $strRepository - Name of the repository
   * @return [array] associative array containing responses from
   *                  each settings function
   */
  private function updateSettings($strRepository) {
    $createBranchResponse = $this->createBranch($strRepository, self::DEFAULT_BRANCH);
    /**
     * In here we'll run:
     *  $this->setCodeOwners($strRepository, self::CODEOWNERS);
     *  $this->createBranch($strRepository, self::DEFAULT_BRANCH);
     *  $this->setDefaultBranch($strRepository, self::DEFAULT_BRANCH);
     *  $this->setBranchProtection($strRepository, self::PROTECTION_RULES);
     *
     * Note: setCodeOwners MUST be ran before createBranch as createBranch
     *  depends on the repo not being empty and setCodeOwners ensures this
     **/
    return [
      'createBranch' => $createBranchResponse
    ];
  }

  public function createRepository($strRepository) {
    $arrRepositoryInfo = $this->createNewRepository($strRepository);
    $arrRepositorySettings = $this->updateSettings($strRepository);

    return [
      'repository_info' => $arrRepositoryInfo,
      'repository_settings' => $arrRepositorySettings
    ];
  }

  public function updateRepository($strRepository) {
    $this->updateSettings($strRepository);
  }
}
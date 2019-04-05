<?php
/**
 * Helper for Github update functions
 **/

use Canddi\Kommander\Exception\Fatal\ResponseException;

use GuzzleHttp\Exception\RequestException;

class Canddi_Helper_Github
{
  use Canddi_Interface_Singleton;

  const GITHUB_ROOT_URL = 'https://api.github.com/';
  const GITHUB_CODEOWNERS_COMMITMSG = 'Create codeowners file';

  private function __construct () {
    $modelHelperConfig = \Canddi_Helper_Config::getInstance();
    $this->setConfig($modelHelperConfig);

    $this->setUsername($this->getConfig()->getGithubUsername());
    $this->setAccessToken($this->getConfig()->getGithubPAT());
    $this->setOrganisation($this->getConfig()->getOrganisation());
    $this->setCodeOwners($this->getConfig()->getCodeowners());

    $this->guzzleConnection = \Canddi_GuzzleFactory::build(
      self::GITHUB_ROOT_URL,
      $this->getAccessToken()
    );
  }

  /**
   * Makes a call to the Github API
   * @param  [string] $strMethod   - HTTP request method to use
   * @param  [string] $strEndpoint - The endpoint to make the request to
   * @param  array  $arrBody       - An optional array of body parameters to
   *                                  send with the request
   * @return [array]               - Array of data about the response from the server
   */
  private function callApi($strMethod, $strEndpoint, $arrBody = []) {
    try {
      $response = $this->guzzleConnection->request(
        $strMethod,
        self::GITHUB_ROOT_URL . "$strEndpoint",
        [
          'json' => $arrBody,
        ]
      );
    } catch (RequestException $exception) { // Network errors
      $response = $exception->getResponse();
      $body = JSON_decode($response->getBody(), true);
      throw new ResponseException(
        $response->getStatusCode(),
        $body['message'],
        isset($body['errors']) ? $body['errors'] : null
      );
    } catch (ClientException $exception) { // 400 level errors
      $response = $exception->getResponse();
      $body = JSON_decode($response->getBody(), true);
      throw new ResponseException(
        $response->getStatusCode(),
        $body['message'],
        $body['errors']
      );
    }

    return JSON_decode($response->getBody(), true);
  }

  private function createCodeOwners($strRepository)
  {
    $strOrganisation = $this->getOrganisation();
    $strContent = $this->getCodeowners();
    $b64Content = base64_encode($strContent);

    /* This code is pretty confusing, maybe could do with refactoring? */
    try {
      /* If a 404 is returned from this GET we don't need to pass the SHA. */
      $getFileResponse = $this->callApi(
        'GET',
        "repos/$strOrganisation/$strRepository/contents/.github/CODEOWNERS"
      );

      $commitResponse = $this->callApi(
        'PUT',
        "repos/$strOrganisation/$strRepository/contents/.github/CODEOWNERS",
        [
          "message" => self::GITHUB_CODEOWNERS_COMMITMSG,
          "content" => $b64Content,
          "sha" => $getFileResponse["sha"],
        ]
      );

      return true;
    } catch (ResponseException $exception) {
      /* Fallthrough to the request below: */
    }

    /* If for some reason the PUT failed with sha, let's try without */
    $commitResponse = $this->callApi(
      'PUT',
      "repos/$strOrganisation/$strRepository/contents/.github/CODEOWNERS",
      [
        "message" => self::GITHUB_CODEOWNERS_COMMITMSG,
        "content" => $b64Content,
      ]
    );

    return true;
  }

  /**
   * createNewRepository
   * @param  [string] $strRepository - Name of repository to create
   * @return [array] If successful returns array of repository data, else error
   */
  private function createNewRepository($strRepository) {
    $strOrganisation = $this->getOrganisation();

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
  }

  public function createRepository($strRepository) {
    $arrRepositoryInfo = $this->createNewRepository($strRepository);

    $this->updateSettings($strRepository);

    return [
      'repository_info' => $arrRepositoryInfo,
    ];
  }

  public function getCodeowners() {
    return $this->codeowners;
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

  public function setCodeOwners($strCodeowners) {
    $this->codeowners = $strCodeowners;
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
    return $this->updateSettings($strRepository);
  }

  private function updateSettings($strRepository) {
    return [
      "codeOwners" => $this->createCodeOwners($strRepository),
    ];
    /**
     * In here we'll run:
     *  $this->createBranch($strRepository, self::DEFAULT_BRANCH);
     *  $this->setDefaultBranch($strRepository, self::DEFAULT_BRANCH);
     *  $this->setBranchProtection($strRepository, self::PROTECTION_RULES);
     **/
  }
}
<?php
/**
 * Helper for Github update functions
 **/

use Canddi\Kommander\Exception\Fatal\ResponseException;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class Canddi_Helper_Github
{
  use Canddi_Interface_Singleton;

  const GITHUB_ROOT_URL = 'https://api.github.com/';
  const GITHUB_CODEOWNERS_COMMITMSG = 'Create codeowners file';
  const DEFAULT_BRANCH = 'develop';
  const PROTECTION_RULES = [
    'develop' => [
        'required_status_checks' => [
            'strict' => true,
            'contexts' => [
                'continuous-integration/travis-ci',
                'WIP',
            ]
        ],
        'enforce_admins' => true,
        'required_pull_request_reviews' => [
            'require_code_owner_reviews' => true,
        ],
        'restrictions' => [
            'users' => [

            ],
            'teams' => [
                'canmergetodev',
            ],
        ],
    ],
    'master' => [
        'required_status_checks' => [
            'strict' => true,
            'contexts' => [
                'continuous-integration/travis-ci',
                'WIP',
            ]
        ],
        'enforce_admins' => true,
        'required_pull_request_reviews' => [
            'require_code_owner_reviews' => true,
        ],
        'restrictions' => [
            'users' => [
                'timlangley',
            ],
            'teams' => [

            ],
        ],
    ],
  ];

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

  /**
   * If doesn't exist, create a branch (this is usually develop)
   * @param  [str] $strRepository - Name of repository
   * @param  [str] $strBranchName - Name of branch to create
   * @return void
   */
  private function createBranch($strRepository, $strBranchName)
  {
    $strOrganisation = $this->getOrganisation();

    try {
      $this->callApi(
        'GET',
        "repos/$strOrganisation/$strRepository/branches/$strBranchName"
      );
      // if this doesn't error, the branch exists, so we can exit.
      return true;
    } catch (ResponseException $exception) {
      // do nothing here, just continue
    }

    // get the hash
    $getHashResponse = $this->callApi(
      'GET',
      "repos/$strOrganisation/$strRepository/git/refs/heads"
    );

    // create the branch
    $createBranchResponse = $this->callApi(
      'POST',
      "repos/$strOrganisation/$strRepository/git/refs",
      [
        "ref" => "refs/heads/$strBranchName",
        "sha" => $getHashResponse[0]['object']['sha']
      ]
    );

    return true;
  }

  private function createBranchProtection($strRepository, $arrRules) {
    $strOrganisation = $this->getOrganisation();

    foreach ($arrRules as $strBranchName) {
        $defaultBranchResponse = $this->callApi(
          'PUT',
          "repos/$strOrganisation/$strRepository/branches/$strBranchName/protection",
          $arrRules[$strBranchName]
        );
    }

    return true;
  }
  private function createDefaultBranch($strRepository, $strBranchName)
  {
    $strOrganisation = $this->getOrganisation();

    $defaultBranchResponse = $this->callApi(
      'PATCH',
      "repos/$strOrganisation/$strRepository",
      [
        'name' => $strRepository,
        'default_branch' => $strBranchName,
      ]
    );

    return true;
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
        'name' => $strRepository,
        'private' => true,
      ]
    );

    return [
      'id' => $response['id'],
      'name' => $response['name'],
      'private' => $response['private'],
    ];
  }

  public function createRepository($strRepository) {
    $arrRepositoryInfo = $this->createNewRepository($strRepository);
    $arrRepositorySettings = $this->updateSettings($strRepository);

    return [
      'details' => $arrRepositoryInfo,
      'settings' => $arrRepositorySettings
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

  public function setAccessToken($strAccessToken) {
    $this->access_token = $strAccessToken;
  }

  public function setCodeOwners($strCodeowners) {
    $this->codeowners = $strCodeowners;
  }

  public function setConfig($modelHelperConfig) {
    $this->config = $modelHelperConfig;
  }

  public function setOrganisation($strOrganisation) {
    $this->organisation = $strOrganisation;
  }

  public function setUsername($strUsername) {
    $this->username = $strUsername;
  }

  public function updateRepository($strRepository) {
    return [
      'settings' => $this->updateSettings($strRepository)
    ];
  }

  /**
   * Updates a repository's settings to match the required settings
   * @param  [string] $strRepository - Name of the repository
   * @return [array] associative array containing responses from
   *                  each settings function
   */
  private function updateSettings($strRepository) {
    return [
        "codeOwners" => $this->createCodeOwners($strRepository),
        "createBranch" => $this->createBranch($strRepository, self::DEFAULT_BRANCH),
        "defaultBranch" => $this->createDefaultBranch($strRepository, self::DEFAULT_BRANCH),
        "branchProtection" => $this->createBranchProtection($strRepository, self::PROTECTION_RULES),
    ];
  }
}
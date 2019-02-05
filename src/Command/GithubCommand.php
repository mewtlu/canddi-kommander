<?php

namespace Dblencowe\CanddiKommander\Command;

use Github\Client;
use Symfony\Component\Console\Command\Command;

abstract class GithubCommand extends Command
{
    protected $githubClient;

    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
        parent::__construct(self::$defaultName);
    }

    protected function authenticate()
    {
        $app = $this->getApplication();
        $this->githubClient->authenticate(
            $app->getEnv()->get('GITHUB_USERNAME'),
            $app->getEnv()->get('GITHUB_PAT'),
            Client::AUTH_HTTP_PASSWORD
        );
    }
}
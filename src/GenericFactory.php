<?php

namespace Dblencowe\CanddiKommander;

use Dblencowe\CanddiKommander\Handler\Env;
use Github\Client;

class GenericFactory
{
    public static function makeEnv(string $envPath)
    {
        $dotenv = \Dotenv\Dotenv::create($envPath);
        $dotenv->load();

        return new Env();
    }

    public static function makeGitHubClient()
    {
        return new Client();
    }
}
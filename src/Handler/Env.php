<?php

namespace CanddiKommander\Handler;

use CanddiKommander\Exception\EnvironmentException;

class Env
{
    private $environment;

    public function __construct()
    {
        $this->environment = $_ENV;
        if (empty($this->environment)) {
            throw new EnvironmentException('Unable to load environment', EnvironmentException::CODE_COMPLETE_ENV_FAILURE);
        }
    }

    public function get(string $key): string
    {
        if (isset($this->environment[$key])) {

            return $this->environment[$key];
        }

        throw new EnvironmentException("Could not find environment variable $key", EnvironmentException::CODE_MISSING_ENV);
    }

    public function set(string $key, $value): Env
    {
        $this->environment[$key] = $value;

        return $this;
    }
}

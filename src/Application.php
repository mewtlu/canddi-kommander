<?php

namespace Dblencowe\CanddiKommander;

use Dblencowe\CanddiKommander\Exception\ApplicationException;
use Symfony\Component\Console\Output\ConsoleOutput;

class Application extends \Symfony\Component\Console\Application
{
    private $storagePath;
    private $env;
    private $internalStoragePath;

    public function __construct(string $storagePath = null)
    {
        $this->internalStoragePath = __DIR__ . '/../storage';
        $this->storagePath = $storagePath;
        if (! $storagePath) {
            $this->storagePath = $_SERVER['HOME'] . '/.canddi';
        }

        $name = 'CanddiKommander';
        $version = '0.0.1';
        parent::__construct($name, $version);
        $this->init();
        $this->registerCommands();
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function getStoragePath()
    {
        return $this->storagePath;
    }

    public function getInternalStoragePath()
    {
        return $this->internalStoragePath;
    }

    private function init()
    {
        if (! is_dir($this->storagePath) &&
            ! mkdir($this->storagePath, 0777, true) &&
            ! is_dir($this->storagePath)) {
            throw new ApplicationException('Could not create application storage path at ' . $this->storagePath);
        }

        if (! is_file($this->storagePath . '/.env')) {
            copy($this->internalStoragePath . '/env.example', $this->storagePath . '/.env');
            $output = new ConsoleOutput();
            $output->writeln('.env file copied to ' . $this->storagePath);
            unset($output);
        }

        $this->env = GenericFactory::makeEnv($this->storagePath);
    }

    private function registerCommands()
    {
        $this->add(new \Dblencowe\CanddiKommander\Command\HelloWorld());
        $this->add(new \Dblencowe\CanddiKommander\Command\CreateGithubRepo(GenericFactory::makeGitHubClient()));
        $this->add(new \Dblencowe\CanddiKommander\Command\UpdateSkeleton(GenericFactory::makeGitHubClient()));
        $this->add(new \Dblencowe\CanddiKommander\Command\RunCommandAgainstRepoCollection(GenericFactory::makeGitHubClient()));
        $this->add(new \Dblencowe\CanddiKommander\Command\CreateDefaultBranches(GenericFactory::makeGitHubClient()));
    }
}
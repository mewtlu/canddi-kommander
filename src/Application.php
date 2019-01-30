<?php

namespace Dblencowe\CanddiKommander;

use Dblencowe\CanddiKommander\Exception\ApplicationException;

class Application extends \Symfony\Component\Console\Application
{
    private $storagePath;
    private $commands = [
        \Dblencowe\CanddiKommander\Command\HelloWorld::class,
    ];

    public function __construct(string $storagePath = null)
    {
        $name = 'CanddiKommander';
        $version = '0.0.1';
        parent::__construct($name, $version);

        $this->storagePath = $storagePath;
        if (! $storagePath) {
            $this->storagePath = $_SERVER['HOME'] . '/.canddi';
        }
        $this->init();
        $this->registerCommands();
    }

    private function init()
    {
        if (! is_dir($this->storagePath) &&
            ! mkdir($this->storagePath, 0777, true) &&
            ! is_dir($this->storagePath)) {
            throw new ApplicationException('Could not create application storage path at ' . $this->storagePath);
        }
    }

    private function registerCommands()
    {
        foreach($this->commands as $command) {
            $this->add(new $command());
        }
    }
}
<?php

namespace Dblencowe\CanddiKommander\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorld extends Command
{
    protected static $defaultName = 'general:hello-world';

    protected function configure()
    {
        $this
            ->setDescription('A standard hello world command used for examples')
            ->setHelp('A simple command used to demonstrate how to build new ones');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello world!');
    }
}
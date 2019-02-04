<?php

namespace Dblencowe\CanddiKommander\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDefaultBranches extends GithubCommand
{
    protected static $defaultName = 'git:create-default-branches';
    private $branches = ['master', 'develop'];

    protected function configure()
    {
        $this
            ->setDescription('Create default branches on repository')
            ->setHelp('Creates the following branches on the specified repository: ' . implode(', ', $this->branches))
            ->addArgument('organisation', InputArgument::REQUIRED, 'The organisation the repository belongs to')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the repository');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->authenticate();
        $owner = $input->getArgument('organisation');
        $name = $input->getArgument('name');
        $latestSha = $this->githubClient->repo()->commits()->all($owner, $name, [])[0]['sha'];
        foreach($this->branches as $branchName) {
            $data = ['ref' => 'refs/heads/' . $branchName, 'sha' => $latestSha];
            try {
                $this->githubClient->gitData()->references()->create($owner, $name, $data);
            } catch(\RuntimeException $e) {
                if ($e->getMessage() !== 'Reference already exists') {
                    throw $e;
                }

                $output->writeln("<info>$branchName already exists. Skipping.</info>" . PHP_EOL);
            }
        }
    }
}
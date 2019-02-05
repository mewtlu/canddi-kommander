<?php

namespace Dblencowe\CanddiKommander\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateGithubRepo extends GithubCommand
{
    protected static $defaultName = 'git:create-repo';
    private $output;
    private $branches = ['develop', 'master'];


    protected function configure()
    {
        $this
            ->setDescription('Create a new repository on GitHub')
            ->setHelp('Create a new repository on GitHub and optionally copy over skeleton')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the repository to be created')
            ->addArgument('public', InputArgument::OPTIONAL, 'Whether or not the repository should be public (default false)')
            ->addArgument('organisation', InputArgument::OPTIONAL, 'The organisation to create the repository within');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->authenticate();
        $repo = $this->createRepository(
            $input->getArgument('name'),
            $input->getArgument('public') ?? false,
            $input->getArgument('organisation') ?? null
        );
        $this->commitSkeleton($repo['owner']['login'], $repo['name']);
        $this->createBranches($repo['owner']['login'], $repo['name']);

        $output->write('Created repository successfully at ' . $repo['html_url']);
    }

    private function createRepository(string $name, bool $public = false, ?string $organisation = null): array
    {
        $response = $this->githubClient->repos()->create(
            $name,
            '',
            '',
            $public,
            $organisation
        );

        return $response;
    }

    private function commitSkeleton(string $owner, string $name)
    {
        $app = $this->getApplication();
        $command = $app->find('git:sync-skeleton');
        foreach ($this->branches as $branchName) {
            $input = new ArrayInput([
                'command' => 'git:sync-skeleton',
                'organisation' => $owner,
                'name' => $name,
                'branch' => $branchName,
            ]);

            $command->run($input, $this->output);
        }
    }

    private function createBranches(string $owner, string $name)
    {
        $app = $this->getApplication();
        $command = $app->find('git:create-default-branches');
        $input = new ArrayInput([
            'command' => 'git:create-default-branches',
            'organisation' => $owner,
            'name' => $name,
        ]);

        $command->run($input, $this->output);
    }
}
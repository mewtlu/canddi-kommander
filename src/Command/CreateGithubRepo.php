<?php

namespace CanddiKommander\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateGithubRepo extends GithubCommand
{
    protected static $defaultName = 'git:create-repo';
    private $output;
    private $branches = ['develop', 'master'];

    private function commitSkeleton(string $owner, string $name)
    {
        $app = $this->getApplication();

        $command = $app->find('git:sync-skeleton');
        $input = new ArrayInput([
            'command' => 'git:sync-skeleton',
            'organisation' => $owner,
            'name' => $name,
            'branch' => 'develop',
        ]);

        $command->run($input, $this->output);
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
    protected function configure()
    {
        $this
            ->setDescription('Create a new repository on GitHub')
            ->setHelp('Create a new repository on GitHub and optionally copy over skeleton')
            ->addArgument('organisation', InputArgument::REQUIRED, 'The organisation to create the repository within')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the repository to be created')
            ->addArgument('public', InputArgument::OPTIONAL, 'Whether or not the repository should be public (default false)');
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
        //Upload the correct skeleton files
        $this->commitSkeleton($repo['owner']['login'], $repo['name']);

        //NOTE you must create branches before applying skeleton
        $this->createBranches($repo['owner']['login'], $repo['name']);

        $output->write(
            'Created repository successfully at ' . $repo['html_url'].
            chr(10).chr(10).
            'Now wait a 10 seconds and run sync s.t settings bot can catch up'.
            chr(10).chr(10)
        );
    }
}

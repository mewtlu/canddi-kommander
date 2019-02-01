<?php

namespace Dblencowe\CanddiKommander\Command;

use Dblencowe\CanddiKommander\Application;
use Github\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateGithubRepo extends Command
{
    protected static $defaultName = 'git:create-repo';
    private $githubClient;
    /** @var OutputInterface $output */
    private $output;
    private $branches = ['develop', 'master'];
    private $app;

    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
        parent::__construct(self::$defaultName);
    }

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

    private function authenticate()
    {
        /** @var Application $app */
        $app = $this->getApplication();
        $this->githubClient->authenticate(
            $app->getEnv()->get('GITHUB_USERNAME'),
            $app->getEnv()->get('GITHUB_PAT'),
            Client::AUTH_HTTP_PASSWORD
        );
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
        $latestSha = $this->githubClient->repo()->commits()->all($owner, $name, [])[0]['sha'];
        foreach($this->branches as $branchName) {
            $data = ['ref' => 'refs/heads/' . $branchName, 'sha' => $latestSha];
            try {
                $this->githubClient->gitData()->references()->create($owner, $name, $data);
            } catch(\RuntimeException $e) {
                if ($e->getMessage() !== 'Reference already exists') {
                    throw $e;
                }

                $this->output->writeln("<info>$branchName already exists. Skipping.</info>");
            }
        }
    }
}
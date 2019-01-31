<?php

namespace Dblencowe\CanddiKommander\Command;

use Dblencowe\CanddiKommander\Application;
use Github\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateGithubRepo extends Command
{
    protected static $defaultName = 'git:create-repo';
    private $githubClient;
    private $branches = [
        'develop',
        'master'
    ];
    private $defaultBranch = 'develop';

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
        $this->authenticate();
        $repo = $this->createRepository(
            $input->getArgument('name'),
            $input->getArgument('public') ?? false,
            $input->getArgument('organisation') ?? null
        );
        $this->commitSkeleton($repo['owner']['login'], $repo['name']);
        $this->createBranches('visualsignal', 'test');

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
        $path = $this->getApplication()->getInternalStoragePath() . '/skeleton/';
        $files = $this->getDirectoryContents($path);
        foreach($files as $filePath) {
            $repoPath = str_replace(realpath($path) . '/', '', $filePath);
            $this->githubClient->api('repo')->contents()->create(
                $owner,
                $name,
                $repoPath,
                file_get_contents($filePath),
                "Added $repoPath to the repository",
                'master'
            );
        }
    }

    private function getDirectoryContents(string $directory, array &$results = []): array
    {
        $files = scandir($directory, SCANDIR_SORT_ASCENDING);
        foreach ($files as $key => $value) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $value);
            if (! is_dir($path)) {
                $results[] = $path;
                continue;
            }

            if (! in_array($value, ['.', '..'])) {
                $this->getDirectoryContents($path, $results);
            }
        }

        return $results;
    }

    private function createBranches(string $owner, string $name)
    {
        $latestSha = $this->githubClient->repo()->commits()->all($owner, $name, [])[0]['sha'];
        foreach($this->branches as $branchName) {
            $data = ['ref' => 'refs/heads/' . $branchName, 'sha' => $latestSha];
            $this->githubClient->gitData()->references()->create($owner, $name, $data);
        }
    }
}
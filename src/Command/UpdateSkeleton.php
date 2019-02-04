<?php

namespace Dblencowe\CanddiKommander\Command;

use Github\Client;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSkeleton extends GithubCommand
{
    protected static $defaultName = 'git:sync-skeleton';

    protected function configure()
    {
        $this
            ->setDescription('Sync repository skeleton to Github')
            ->setHelp('Copy over skeleton files')
            ->addArgument('organisation', InputArgument::REQUIRED, 'The organisation the repository belongs to')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the repository')
            ->addArgument('branch', InputArgument::REQUIRED, 'An existing branch on the repository to commit the files to');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->authenticate();
        $path = $this->getApplication()->getInternalStoragePath() . '/skeleton/';
        $files = $this->getDirectoryContents($path);
        $owner = $input->getArgument('organisation');
        $name = $input->getArgument('name');
        $branch = $input->getArgument('branch');

        foreach($files as $filePath) {
            $repoPath = str_replace(realpath($path) . '/', '', $filePath);
            $contents = file_get_contents($filePath);
            if ($repoPath === '.github/settings.yml') {
                $contents = str_replace('{{ REPO_NAME }}', $input->getArgument('name'), $contents);
            }

            try {
                $oldFile = $this->githubClient->repo()->contents()->show(
                    $owner,
                    $name,
                    $repoPath,
                    $branch
                );

                $this->updateFile($owner, $name, $repoPath, $contents, $branch, $oldFile['sha']);
            } catch(\RuntimeException $e) {
                if (! in_array($e->getMessage(), ['Not Found', 'This repository is empty.'])) {
                    throw $e;
                }

                $this->createFile($owner, $name, $repoPath, $contents, $branch);
            }
        }

        $output->write('Synched skeleton to repository on branch ' . $input->getArgument('branch'));
    }

    private function createFile(string $owner, string $name, string $repoPath, string $contents, string $branch)
    {
        $this->githubClient->repo()->contents()->create(
            $owner,
            $name,
            $repoPath,
            $contents,
            "Added $repoPath to the repository",
            $branch
        );
    }

    private function updateFile(string $owner, string $name, string $repoPath, string $contents, string $branch, string $sha)
    {
        $this->githubClient->repo()->contents()->update(
            $owner,
            $name,
            $repoPath,
            $contents,
            "Update $repoPath",
            $sha,
            $branch
        );
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
}
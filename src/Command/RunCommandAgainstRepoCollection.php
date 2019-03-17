<?php

namespace CanddiKommander\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommandAgainstRepoCollection extends GithubCommand
{
    public static $defaultName = 'git:sync-skeletons';
    private $output;

    protected function configure()
    {
        $this
            ->setDescription('Sync repository skeleton to several Github repositories')
            ->setHelp('Copy over skeleton files to repositories matching a pattern')
            ->addArgument('organisation', InputArgument::REQUIRED, 'The organisation the repository belongs to')
            ->addArgument('filter', InputArgument::OPTIONAL, 'Optional search term to filter repository list by');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->authenticate();
        $term = $input->getArgument('filter') ?? '';
        $repositories = $this->githubClient->search()->repositories($term . ' in:name org:' . $input->getArgument('organisation'));
        foreach($repositories['items'] as $repo) {
            $this->commitSkeleton($input->getArgument('organisation'), $repo['name'], $repo['default_branch']);
            $this->createDefaultBranches($input->getArgument('organisation'), $repo['name']);
            $output->writeln(sprintf('Synched skeleton to %s/%s on branch %s', $input->getArgument('organisation'), $repo['name'], $repo['default_branch']));
        }
        $output->writeln('<info>Command finished!</info>');
    }

    private function createDefaultBranches(string $owner, string $name)
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

    private function commitSkeleton(string $owner, string $name, string $branch)
    {
        $app = $this->getApplication();
        $command = $app->find('git:sync-skeleton');
        $input = new ArrayInput([
            'command' => 'git:sync-skeleton',
            'organisation' => $owner,
            'name' => $name,
            'branch' => $branch,
        ]);

        $command->run($input, new NullOutput());
    }
}

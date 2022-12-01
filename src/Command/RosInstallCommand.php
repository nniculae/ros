<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\RuntimeException;

#[AsCommand(
    name: 'ros:install',
    description: 'Installs ROS in your environment.',
)]
final class RosInstallCommand extends Command
{
    private array $commands = [
        [
            'name' => 'doctrine:database:drop',
            'message' => 'Drop the database if exists.',
            'arguments' => [
                '--force' => true,
            ],
        ],
        [
            'name' => 'doctrine:database:create',
            'message' => 'Creating the database.',
            'arguments' => [],
        ],

        [
            'name' => 'doctrine:migrations:migrate',
            'message' => ' Executing migrations.',
            'arguments' => [],
        ],
        [
            'name' => 'doctrine:fixtures:load',
            'message' => 'Loading fixtures.',
            'arguments' => [],
        ],
        [
            'name' => 'lexik:jwt:generate-keypair',
            'message' => 'Configure JWT.',
            'arguments' => [
                '--overwrite' => true,
            ],
        ],
    ];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $outputStyle = new SymfonyStyle($input, $output);

        $outputStyle->writeln('<info>Installing ROS...</info>');

        $errored = false;

        /**
         * @var int   $step
         * @var array $commandModel
         */
        foreach ($this->commands as $step => $commandModel) {
            try {
                $outputStyle->newLine();
                $outputStyle->section(sprintf(
                    'Step %d of %d. <info>%s</info>',
                    $step + 1,
                    \count($this->commands),
                    $commandModel['message'],
                ));

                $commandName = $commandModel['name'];

                $command = $this->getApplication()->find($commandName);

                $arguments = $commandModel['arguments'];
                $arguments['--no-interaction'] = true;

                $inputArguments = new ArrayInput($arguments);
                $inputArguments->setInteractive(false);

                $command->run($inputArguments, $output);
            } catch (RuntimeException) {
                $errored = true;
            }
        }

        $outputStyle->newLine(2);
        $outputStyle->success($this->getFinalMessage($errored));

        return $errored ? Command::FAILURE : Command::SUCCESS;
    }

    private function getFinalMessage(bool $errored): string
    {
        if ($errored) {
            return 'ROS has been installed, but some error occurred.';
        }

        return 'ROS has been successfully installed.';
    }
}

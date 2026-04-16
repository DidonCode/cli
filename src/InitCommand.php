<?php

namespace OpenMicroservice;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'open-microservice:init', description: 'Initialise la structure de base du projet')]
class InitCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::OPTIONAL, 'Chemin du projet', './');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = rtrim($input->getArgument('path'), '/');

        $dirs = [
            "$path/services",
            "$path/config",
            "$path/gateway",
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
                $output->writeln("  <info>Créé :</info> $dir");
            }
        }

        $output->writeln("<comment>Projet initialisé avec succès !</comment>");
        return Command::SUCCESS;
    }
}
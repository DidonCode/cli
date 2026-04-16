<?php

namespace OpenMicroservice;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'open-microservice:add', description: 'Ajoute un nouveau service')]
class AddServiceCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Nom du service');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $servicePath = "services/$name";

        if (is_dir($servicePath)) {
            $output->writeln("<error>Le service $name existe déjà !</error>");
            return Command::FAILURE;
        }

        // Création du dossier et d'un fichier d'exemple
        mkdir($servicePath, 0777, true);
        file_put_contents("$servicePath/index.php", "<?php\n\necho 'Service $name running';");

        $output->writeln("<info>Service '$name' ajouté dans $servicePath</info>");
        return Command::SUCCESS;
    }
}
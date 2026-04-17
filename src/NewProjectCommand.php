<?php

namespace OpenMicroservice;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'new', description: 'Crée un nouveau projet microservice')]
class NewProjectCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Nom du projet à créer');
        $this->addArgument('path', InputArgument::OPTIONAL, 'Dossier parent', '.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = trim((string) $input->getArgument('name'));
        $parentPath = rtrim((string) $input->getArgument('path'), DIRECTORY_SEPARATOR);
        $projectPath = $parentPath === '.' ? $name : $parentPath . DIRECTORY_SEPARATOR . $name;

        if (is_dir($projectPath)) {
            $output->writeln("<error>Le dossier $projectPath existe déjà !</error>");
            return Command::FAILURE;
        }

        $this->createProjectSkeleton($projectPath);

        $output->writeln("<info>Projet créé :</info> $projectPath");
        $output->writeln('<comment>Installe ensuite les dépendances avec composer install</comment>');

        return Command::SUCCESS;
    }

    private function createProjectSkeleton(string $projectPath): void
    {
        $directories = [
            $projectPath,
            $projectPath . '/src',
            $projectPath . '/config',
            $projectPath . '/services',
            $projectPath . '/gateway',
            $projectPath . '/public',
        ];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
        }

        file_put_contents(
            $projectPath . '/composer.json',
            $this->generateComposerJson($projectPath)
        );

        file_put_contents(
            $projectPath . '/README.md',
            "# " . basename($projectPath) . "\n\nProjet microservice généré avec Open Microservice.\n"
        );

        file_put_contents(
            $projectPath . '/.gitignore',
            "vendor/\n.env\n"
        );

        file_put_contents(
            $projectPath . '/public/index.php',
            "<?php\n\nrequire __DIR__ . '/../vendor/autoload.php';\n\necho 'Open Microservice app is running';\n"
        );

        file_put_contents(
            $projectPath . '/src/App.php',
            "<?php\n\nnamespace App;\n\nclass App\n{\n}\n"
        );

        file_put_contents(
            $projectPath . '/.env.example',
            "APP_ENV=dev\nAPP_DEBUG=1\n"
        );
    }

    private function generateComposerJson(string $projectPath): string
    {
        $projectName = basename($projectPath);

        return json_encode(
            [
                'name' => 'app/' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $projectName)),
                'description' => 'Microservice generated with Open Microservice',
                'require' => [
                    'php' => '>=8.0',
                ],
                'autoload' => [
                    'psr-4' => [
                        'App\\' => 'src/',
                    ],
                ],
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ) . "\n";
    }
}
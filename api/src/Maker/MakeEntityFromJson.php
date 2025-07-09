<?php

namespace App\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(name: 'app:entity-from-json', description: 'Generates an API resource entity from a JSON or YAML file')]
class MakeEntityFromJson extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'app:entity-from-json';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command->addOption('file', null, InputOption::VALUE_OPTIONAL, 'Path to the JSON or YAML entity definition file');
        $command->addOption('data', null, InputOption::VALUE_OPTIONAL, 'JSON data as string for entity generation');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $data = $input->getOption('data');
        $file = $input->getOption('file');

        // Get content either from file or data
        if ($file && $data) { // If both options are provided, throw an error
            throw new \InvalidArgumentException('You can only provide either --file or --data, not both.');
        }
        elseif ($file) {
            // Validate file existence
            if (!file_exists($file)) {
                throw new \InvalidArgumentException("File not found: $file");
            }

            // Get file type
            $type = $type ?? strtolower(pathinfo($file, PATHINFO_EXTENSION));
            // Get contents from file
            $data = file_get_contents($file);

        } elseif ($data) {
            if (empty($data)) {
                throw new \InvalidArgumentException('No data provided.');
            }
            $type = 'json'; // Default to JSON if data is provided
        } else { // If neither option is provided, throw an error
            throw new \InvalidArgumentException('Either --file or --data option must be provided.');
        }


        // Get data from content
        $data = match ($type) {
            'json' => json_decode($data, true),
            'yaml', 'yml' => Yaml::parse($data),
            default => throw new \InvalidArgumentException("Unsupported file type: $type"),
        };

        // Validate data
        if (!isset($data['name'], $data['fields'])) {
            throw new \InvalidArgumentException("Definition must contain 'name' and 'fields'. " . print_r($data, true));
        }

        // Generate entity class
        // Build fields block
        $fieldsCode = '';
        foreach ($data['fields'] as $f) {
            $fieldsCode .= sprintf(
                "    #[ORM\Column(type: \"%s\", nullable: %s)]\n    private \$%s;\n\n",
                $f['type'],
                $f['nullable'] ? 'true' : 'false',
                $f['name']
            );
        }

        // Create class name details
        $classNameDetails = $generator->createClassNameDetails($data['name'], 'Entity\\');

        // Generate the class file
        $generator->generateClass(
            $classNameDetails->getFullName(),
            __DIR__ . '/../Resources/skeleton/Entity.tpl.php',
            [
                'namespace' => 'App\Entity',
                'use_statements' => $data['apiResource'] ? "use ApiPlatform\\Metadata\\ApiResource;\n" : '',
                'repository_class_name' => $classNameDetails->getShortName() . 'Repository',
                'class_name' => $classNameDetails->getShortName(),
                'api_resource' => $data['apiResource'] ?? false,
                'additional_fields' => $fieldsCode,
            ]
        );

        $generator->writeChanges();
        $io->success('Entity generated.');

        // migrations:diff
        $processDiff = new Process(['php', 'bin/console', 'doctrine:migrations:diff']);
        $processDiff->setTty(false);
        $processDiff->run(function ($type, $buffer) use ($io) {
            $io->writeln($buffer);
        });
        if (!$processDiff->isSuccessful()) {
            $io->error('doctrine:migrations:diff failed: ' . $processDiff->getErrorOutput());
            return;
        }

        // migrations:migrate
        $processMigrate = new Process(['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction']);
        $processMigrate->setTty(false);
        $processMigrate->run(function ($type, $buffer) use ($io) {
            $io->writeln($buffer);
        });
        if (!$processMigrate->isSuccessful()) {
            $io->error('doctrine:migrations:migrate failed: ' . $processMigrate->getErrorOutput());
            return;
        }

        $io->success('Migrations created and applied successfully.');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(
            \ApiPlatform\Metadata\ApiResource::class,
            'api-platform/core'
        );
    }
}

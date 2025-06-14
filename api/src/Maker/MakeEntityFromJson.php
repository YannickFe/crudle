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
use Symfony\Component\Yaml\Yaml;

#[AsCommand(name: 'make:entity-from-json', description: 'Generates an API resource entity from a JSON or YAML file')]
class MakeEntityFromJson extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:entity-from-json';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command->addOption('from', null, InputOption::VALUE_REQUIRED, 'Path to the JSON or YAML entity definition');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $path = $input->getOption('from');
        if (!file_exists($path)) {
            throw new \RuntimeException("File not found: $path");
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $data = match ($ext) {
            'json' => json_decode(file_get_contents($path), true),
            'yaml', 'yml' => Yaml::parseFile($path),
            default => throw new \InvalidArgumentException("Unsupported file type: $ext"),
        };

        if (!isset($data['name'], $data['fields'])) {
            throw new \InvalidArgumentException("Definition file must contain 'name' and 'fields'");
        }

        // build fields block
        $fieldsCode = '';
        foreach ($data['fields'] as $f) {
            $fieldsCode .= sprintf(
                "    #[ORM\Column(type: \"%s\", nullable: %s)]\n    private \$%s;\n\n",
                $f['type'],
                $f['nullable'] ? 'true' : 'false',
                $f['name']
            );
        }

        $classNameDetails = $generator->createClassNameDetails($data['name'], 'Entity\\');

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
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(
            \ApiPlatform\Metadata\ApiResource::class,
            'api-platform/core'
        );
    }
}

<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern;

use Illuminate\Console\Command;

final class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name} {--traits=} {--interface=}';

    protected $description = 'Create a new repository class in app/Repositories with optional traits and interface.';

    public function handle()
    {
        $name = $this->argument('name');
        $className = $name . 'Repository';
        $directory = app_path('Repositories');
        $path = $directory . '/' . $className . '.php';
        $model = $name;
        $modelNamespace = 'App\\Models\\' . $model;
        $namespace = 'App\\Repositories';

        $traits = $this->option('traits') ? explode(',', $this->option('traits')) : [];
        $traitsUse = '';
        $traitsList = '';
        if ($traits && $traits[0] !== '') {
            foreach ($traits as $trait) {
                $traitsUse .= "use Laravelplus\\RepositoryPattern\\Traits\\$trait;\n";
                $traitsList .= ($traitsList ? ', ' : ' ') . "$trait";
            }
        }
        $interface = $this->option('interface') ? ' implements ' . $this->option('interface') : '';

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($path)) {
            $this->error("Repository {$className} already exists!");

            return 1;
        }

        $useSimpleStub = empty($traitsList) && !$this->option('interface');
        $stubPath = $useSimpleStub
            ? (file_exists(base_path('stubs/repository.simple.stub')) ? base_path('stubs/repository.simple.stub') : __DIR__ . '/../stubs/repository.simple.stub')
            : (file_exists(base_path('stubs/repository.stub')) ? base_path('stubs/repository.stub') : __DIR__ . '/../stubs/repository.stub');
        $stub = file_get_contents($stubPath);
        $stub = str_replace([
            '{{ namespace }}',
            '{{ class }}',
            '{{ model }}',
            '{{ modelNamespace }}',
            '{{ traitsUse }}',
            '{{ traits }}',
            '{{ interface }}',
        ], [
            $namespace,
            $className,
            $model,
            $modelNamespace,
            $traitsUse,
            $traitsList,
            $interface,
        ], $stub);

        file_put_contents($path, $stub);
        $this->info("Repository created: app/Repositories/{$className}.php");

        return 0;
    }
}

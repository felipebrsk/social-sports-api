<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Attributes\{
    Signature,
    Description,
};

#[Signature('make:builder {name : The name of the builder class} {--f|force : Force the creation if file exists}')]
#[Description('Create a new Repository and Service structure.')]
class BuilderCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        $this->info("Generating files for {$name}...");

        $templates = [
            'repository-interface' => app_path("Contracts/Repositories/{$name}RepositoryInterface.php"),
            'repository' => app_path("Repositories/{$name}Repository.php"),
            'service-interface' => app_path("Contracts/Services/{$name}ServiceInterface.php"),
            'service' => app_path("Services/{$name}Service.php"),
        ];

        foreach ($templates as $stub => $fullPath) {
            $this->createFileFromStub($stub, $fullPath, $name);
        }

        $this->info("Builder structure for {$name} created successfully!");
    }

    /**
     * Create a file from a specific stub.
     */
    protected function createFileFromStub(string $stubName, string $fullPath, string $class): void
    {
        if (File::exists($fullPath) && ! $this->option('force')) {
            $this->error("File already exists: {$fullPath}");

            return;
        }

        $directory = dirname($fullPath);
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $stubPath = base_path("stubs/{$stubName}.stub");

        if (! File::exists($stubPath)) {
            $this->error("Stub not found at: {$stubPath}");

            return;
        }

        $stubContent = File::get($stubPath);

        $content = str_replace(
            ['$CLASS$'],
            [$class],
            $stubContent,
        );

        File::put($fullPath, $content);

        $this->line("<info>Created:</info> " . str_replace(base_path() . DIRECTORY_SEPARATOR, '', $fullPath));
    }
}

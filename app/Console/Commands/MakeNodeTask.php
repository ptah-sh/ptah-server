<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeNodeTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:node-task [taskName]';

    /**
     * The console command description.
     *34
     *
     * @var string
     */
    protected $description = 'Create a new node task';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->promptForMissingArguments($this->input, $this->output);

        $taskName = $this->hasArgument('taskName')
            ? $this->argument('taskName')
            : $this->ask('Enter task name');

        $taskName = Str::camel($taskName);
        $taskName = Str::ucfirst($taskName);

        $this->info("Normalized Task Name: {$taskName}");

        $taskDataFiles = [
            "Models/NodeTasks/{$taskName}/{$taskName}Meta.php" => $this->readStub('NodeTaskMeta', $taskName),
            "Models/NodeTasks/{$taskName}/{$taskName}Result.php" => $this->readStub('NodeTaskResult', $taskName),
            "Events/NodeTasks/{$taskName}/{$taskName}Completed.php" => $this->readStub('NodeTaskCompleted', $taskName),
            "Events/NodeTasks/{$taskName}/{$taskName}Failed.php" => $this->readStub('NodeTaskFailed', $taskName),
        ];

        $dirs = [
            "Events/NodeTasks/{$taskName}",
            "Models/NodeTasks/{$taskName}",
        ];

        foreach ($dirs as $dir) {
            $path = app_path($dir);

            $this->info('Creating directory '.$dir);

            mkdir($path);
        }

        foreach ($taskDataFiles as $abstractPath => $template) {
            $path = app_path($abstractPath);

            $this->info('Writing '.$abstractPath);

            file_put_contents($path, $template);
        }

        $this->info('Done!');
    }

    protected function readStub($type, $taskName): string
    {
        $template = file_get_contents(__DIR__."/stubs/MakeNodeTask/$type.stub");

        return str_replace('$taskName', $taskName, $template);
    }
}

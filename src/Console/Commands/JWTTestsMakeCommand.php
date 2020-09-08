<?php

namespace JiteshDhamaniya\JWTTests\Console\Commands;

use Illuminate\Console\Command;

class JWTTestsMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:jwt-tests
    {--f|force : Overwrite existing tests}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create tests for the tymon/jwt-auth';

    /**
     * The tests that need to be exported.
     *
     * @var array
     */
    protected $tests = [
        'Feature/Auth/JWT/JWTAuthorizationTest.php',
    ];

    /**
     * Directories that must be created.
     *
     * @var array
     */
    protected $directories = [
        'tests/Feature/Auth/JWT',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();

        $this->publishTests();

        $this->info('JWT Authentication tests generated successfully.');
    }

    /**
     * Create required directories.
     *
     * @return void
     */
    public function createDirectories()
    {
        foreach ($this->directories as $dir) {
            if (! is_dir($directory = base_path($dir))) {
                mkdir($directory, 0755, true);
            }
        }
    }

    /**
     * Publish all tests.
     *
     * @return void
     */
    public function publishTests()
    {
        $tests = $this->tests;

        foreach ($tests as $test) {
            if (file_exists($destination = base_path('tests/' . $test)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$test}] test already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            $this->publishStub(__DIR__ . '/../stubs/tests/' . $test, $destination);
        }
    }

    /**
     * Publish individual stubs.
     *
     * @return void
     */
    public function publishStub($stubPath, $destinationTest)
    {
        $content = file_get_contents($stubPath);

        file_put_contents($destinationTest, $content);
    }

    /**
     * Get test in snake_case format.
     *
     * @return string
     */
    public function snakeCase($stub)
    {
        return preg_replace_callback('/    public function test.+/', function ($matches) {
            return strtolower(preg_replace('/([A-Z])/', '_$0', $matches[0]));
        }, $stub);
    }

}

<?php

namespace JiteshDhamaniya\JWTTests;


use Illuminate\Support\ServiceProvider;

use JiteshDhamaniya\JWTTests\Console\Commands\JWTTestsMakeCommand;

class JWTTestsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                JWTTestsMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}

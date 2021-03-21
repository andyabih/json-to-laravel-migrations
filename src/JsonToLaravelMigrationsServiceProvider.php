<?php

namespace Andyabih\JsonToLaravelMigrations;

use Illuminate\Support\ServiceProvider;
use Andyabih\JsonToLaravelMigrations\Console\MakeMigrations;

class JsonToLaravelMigrationsServiceProvider extends ServiceProvider {
    public function register() {

    }

    public function boot() {
        if($this->app->runningInConsole()) {
            $this->commands([
                MakeMigrations::class
            ]);
        }
    }
}
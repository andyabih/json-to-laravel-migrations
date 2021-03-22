<?php

namespace Andyabih\JsonToLaravelMigrations\Console;

use Illuminate\Console\Command;
use Andyabih\JsonToLaravelMigrations\JsonToMigration;
use Andyabih\JsonToLaravelMigrations\JsonToRequest;

class MakeMigrations extends Command {
    protected $signature = 'json:migrate {file}';

    protected $description = "Create migrations from JSON file.";

    public function handle() {
        $this->info("Creating migrations...");
        new JsonToMigration($this->argument('file'));
        $this->info("Migrations created!");

        $this->info("Creating Requests...");
        new JsonToRequest($this->argument('file'));
        $this->info("Requests created!");
    }

}
<?php

namespace Andyabih\JsonToLaravelMigrations\Console;

use Illuminate\Console\Command;
use Andyabih\JsonToLaravelMigrations\JsonToMigration;

class MakeMigrations extends Command {
    protected $signature = 'json:migrate {file}';

    protected $description = "Create migrations from JSON file.";

    public function handle() {
        $this->info("Creating migrations...");
        
        new JsonToMigration($this->argument('file'));

        $this->info("Migrations created!");
    }

}
<?php

namespace Andyabih\JsonToLaravelMigrations\Console;

use Illuminate\Console\Command;
use Andyabih\JsonToLaravelMigrations\JsonToMigration;
use Andyabih\JsonToLaravelMigrations\JsonToRequest;
use Andyabih\JsonToLaravelMigrations\JsonParser;


class MakeMigrations extends Command {
    protected $signature = 'json:migrate {file}';
    protected $description = "Create migrations from JSON file.";
    
    public function handle() {
        $this->info("Parsing json File...");
        $json = (new JsonParser($this->argument('file')))->parse();;

        $this->info("Creating migrations...");
        new JsonToMigration($json);
        $this->info("Migrations created!");

        $this->info("Creating Requests...");
        new JsonToRequest($json);
        $this->info("Requests created!");
    }
}
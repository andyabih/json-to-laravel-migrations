<?php

namespace Andyabih\JsonToLaravelMigrations;
  
use Andyabih\JsonToLaravelMigrations\Parsers\SchemaParser;
use Andyabih\JsonToLaravelMigrations\Creators\MigrationCreator;


class JsonToMigration extends Generator{

    protected $methods;

    public function parse() {
        $schemaParser = new SchemaParser($this->schema);
        $this->methods = $schemaParser->parse();
        return $this;
    }

    public function create():void {
        $migrationCreator = new MigrationCreator($this->methods);
        $migrationCreator->create();
    }
}
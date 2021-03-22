<?php

namespace Andyabih\JsonToLaravelMigrations;

use \Illuminate\Support\Collection;
use Andyabih\JsonToLaravelMigrations\Parsers\SchemaParser;
use Andyabih\JsonToLaravelMigrations\Creators\MigrationCreator;


class JsonToMigration extends Parameters {
    /**
     * Array schema of the JSON file
     * 
     * @var array
     */
    public $schema;

    /**
     * Schema migration methods
     */
    protected $methods;

    public function __construct($schema) {
        $this->schema = $schema;
        $this->parse()
        ->create();
    }
    
    private function parse() {
        $schemaParser = new SchemaParser($this->schema);
        $this->methods = $schemaParser->parse();
        return $this;
    }

    private function create() {
        $migrationCreator = new MigrationCreator($this->methods);
        $migrationCreator->create();
    }
}
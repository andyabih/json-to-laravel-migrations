<?php

namespace Andyabih\JsonToLaravelMigrations;

use \Illuminate\Support\Collection;

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
    protected $methods, $requests;

    public function __construct($jsonPath) {
        $this->load($jsonPath);
        $this->parse();
        $this->create();
    }
    
    private function parse() {
        $schemaParser = new SchemaParser($this->schema);
        $validationParser = new ValidationParser($this->schema);

        $this->methods = $schemaParser->parse();
        $this->requests = $validationParser->parse();
    }

    private function load(String $jsonPath) {
        $jsonParser = new JsonParser($jsonPath);
        $this->schema = $jsonParser->parse();
    }

    private function create() {
        $migrationCreator = new MigrationCreator($this->methods);
        $migrationCreator->create();
    }
}
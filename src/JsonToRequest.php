<?php

namespace Andyabih\JsonToLaravelMigrations;

use \Illuminate\Support\Collection;

class JsonToRequest extends Parameters {
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
        $validationParser = new ValidationParser($this->schema);
        $this->requests = $validationParser->parse();
    }

    private function load(String $jsonPath) {
        $jsonParser = new JsonParser($jsonPath);
        $this->schema = $jsonParser->parse();
    }

    private function create() {
        $requestCreator = new RequestCreator($this->requests);
        $requestCreator->create();
    }
}
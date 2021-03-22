<?php

namespace Andyabih\JsonToLaravelMigrations;
use \Illuminate\Support\Collection;
use Andyabih\JsonToLaravelMigrations\Parsers\ValidationParser;
use Andyabih\JsonToLaravelMigrations\Creators\RequestCreator;



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
    protected $requests;

    public function __construct($schema) {
        $this->schema = $schema;
        $this->parse()
        ->create();
    }
    
    private function parse() {
        $validationParser = new ValidationParser($this->schema);
        $this->requests = $validationParser->parse();
        return $this;
    }


    private function create() {
        $requestCreator = new RequestCreator($this->requests);
        $requestCreator->create();
    }
}
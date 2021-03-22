<?php

namespace Andyabih\JsonToLaravelMigrations;

use \Illuminate\Support\Collection;
use Andyabih\JsonToLaravelMigrations\Creators\BackpackControllerCreator;
use Andyabih\JsonToLaravelMigrations\Parsers\BackpackParser;

class JsonToBackpackController{
    /**
     * Array schema of the JSON file
     * 
     * @var array
     */
    public $schema, $cruds;

   


    public function __construct($schema) {
        $this->schema = $schema;

        $this->parse()
        ->create();
    }
    
    private function parse() {
        $schemaParser = new BackpackParser($this->schema);
        $this->cruds = $schemaParser->parse();
        return $this;
    }

    private function create() {
        $backpackControllerCreator = new BackpackControllerCreator($this->cruds);
        $backpackControllerCreator->create();
    }
}
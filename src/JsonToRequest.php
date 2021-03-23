<?php

namespace Andyabih\JsonToLaravelMigrations;

use Andyabih\JsonToLaravelMigrations\Parsers\ValidationParser;
use Andyabih\JsonToLaravelMigrations\Creators\RequestCreator;

class JsonToRequest extends Generator{

    protected $requests;
    
    public function parse() {
        $validationParser = new ValidationParser($this->schema);
        $this->requests = $validationParser->parse();
        return $this;
    }


    public function create(): void {
        $requestCreator = new RequestCreator($this->requests);
        $requestCreator->create();
    }
}
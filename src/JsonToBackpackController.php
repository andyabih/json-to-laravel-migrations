<?php

namespace Andyabih\JsonToLaravelMigrations;

use Andyabih\JsonToLaravelMigrations\Creators\BackpackControllerCreator;
use Andyabih\JsonToLaravelMigrations\Parsers\BackpackParser;

class JsonToBackpackController extends Generator
{
    protected $cruds;

    public function parse()
    {
        $schemaParser = new BackpackParser($this->schema);
        $this->cruds = $schemaParser->parse();
        return $this;
    }

    public function create(): void
    {
        $backpackControllerCreator = new BackpackControllerCreator($this->cruds);
        $backpackControllerCreator->create();
    }
}

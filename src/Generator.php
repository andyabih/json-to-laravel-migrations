<?php
namespace Andyabih\JsonToLaravelMigrations;
use Andyabih\JsonToLaravelMigrations\Contracts\GeneratorInterface; 

abstract class Generator implements GeneratorInterface {
      /**
     * Array schema of the JSON file
     * @var array
     */ 
    public $schema;

    public function __construct($schema) {
        $this->schema = $schema;
        $this->parse()
        ->create();
    }
   
}
<?php

namespace Andyabih\JsonToLaravelMigrations\Parsers;
use Andyabih\JsonToLaravelMigrations\Traits\HasParameterChecks;

class BackpackParser
{
    use HasParameterChecks;

    protected $key = 'backpack';
    protected $schema, $requests = [];

    public function __construct($schema)
    {
        $this->schema = $schema;
        $this->parse();
    }

    public function parse()
    {
        foreach ($this->schema as $table => $columns) {
            foreach ($columns as $column => $parameters) {
                if ($this->wantsParameter($parameters)) {
                    $this->requests[$table][$column] = $parameters[$this->key];
                }
            }
        }
        return $this->requests;
    }
}

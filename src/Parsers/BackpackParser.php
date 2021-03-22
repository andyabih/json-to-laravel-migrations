<?php

namespace Andyabih\JsonToLaravelMigrations\Parsers;

class BackpackParser
{
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
                if ($this->wantsBackpack($parameters)) {
                    $this->requests[$table][$column] = $parameters[$this->key];
                }
            }
        }
        return $this->requests;
    }

    public function wantsBackpack($parameters)
    {
        return isset($parameters[$this->key]);
    }
}

<?php

namespace Andyabih\JsonToLaravelMigrations;

class JsonParser {
    /**
     * Path of the JSON schema
     * 
     * @var string
     */
    protected $path;

    /**
     * Create a new JSON Parser instance
     * 
     * @param string $path
     * @return void
     */
    public function __construct(String $path) {
        $this->path = $path;
        $this->exists();
    }

    /**
     * Parse the JSON file into array
     * 
     * @return array
     */
    public function parse() {
        $json = $this->get();
        $schema = [];

        foreach($json as $table => $columns) {
            $schema[$table] = [];
            
            foreach($columns as $column => $parameters) {
                $parametersList = explode('|', $parameters['migration']);
                $parametersList = array_map(function($parameter) {
                    return explode(':', $parameter);
                }, $parametersList);

                $schema[$table][$column]['migration'] = $parametersList;
                if (isset($parameters['validation'])){
                    $schema[$table][$column]['validation'] = $parameters['validation'];
                }

                if(isset($parameters['backpack'])){
                    $schema[$table][$column]['backpack'] = $parameters['backpack'];
                }

            }
        }   
        return $schema;
    }

    /**
     * Load JSON from file
     * 
     * @return array
     */
    public function get() {
        $json = file_get_contents($this->path);
        return json_decode($json, true);
    }

    /**
     * Check if the path exists
     */
    private function exists() {
        if(!file_exists($this->path)) throw new \Exception("JSON Schema file does not exist.");
    }
}
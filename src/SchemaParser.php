<?php

namespace Andyabih\JsonToLaravelMigrations;

class SchemaParser {
    /**
     * Migration schema as an array
     * 
     * @var array
     */
    protected $schema;

    /**
     * Create a new Schema parser instance
     * 
     * @param array $schema
     */
    public function __construct(Array $schema) {
        $this->schema = $schema;
    }

    /**
     * Parses the schema into migration methods
     * 
     * @return array
     */
    public function parse() {
        $tables = Helpers::justKeys($this->schema);

        foreach($this->schema as $table => $columns) {

            $tables[$table] = $this->generateMethods($columns);
        }
        
        return $tables;
    }

    /**
     * Loops through array of columns to parse
     * 
     * @return array
     */
    private function generateMethods(Array $columns) {
        $methods = [];
        foreach($columns as $column => $parameters) {
            $methods[] = $this->generateMethod($column, $parameters['migration']);
        }
        
        return $methods;
    }
    
    /**
     * Generates a migration method for the column
     */
    private function generateMethod(String $column, Array $parameters) {
        $columnType           = head(head($parameters));
        $modifiers            = array_slice($parameters, 1);
        $columnTypeParameters = array_slice(head($parameters), 1);

        $this->checkIfValidColumnType($columnType);
        
        $baseMethod = $this->generateBaseMethod($column, $columnType, $columnTypeParameters);
        $modifiers  = $this->generateModifiers($modifiers);
        
        return $baseMethod . $modifiers . ';';
    }

    /**
     * Generate the base method for the migration
     * 
     * @param string $column
     * @param string $columnType
     * @param array $columnTypeParameters
     * @return string
     */
    private function generateBaseMethod($column, $columnType, $columnTypeParameters) {
        $validColumnType      = Parameters::getValidColumnType($columnType);
        $methodParameters     = Parameters::getParameters($validColumnType);
        $columnTypeParameters = !empty($columnTypeParameters) ? 
            explode(',', $columnTypeParameters[0]) :
            [];

        $customParameters = [];
        foreach($methodParameters as $k => $parameter) {
            if(!isset($columnTypeParameters[$k])) {
                $customParameters[] = $parameter;
                continue;
            }

            if(is_array($parameter)) {
                $customParameters[] = "['" . implode("', '", $columnTypeParameters) . "']";
            } else {
                $customParameters[] = $columnTypeParameters[$k];
            }
        }

        $joinedParameters = $this->joinParameters($customParameters);
        if(!blank($joinedParameters)) $joinedParameters = ', ' . $joinedParameters;

        return sprintf('$table->%s("%s"%s)', $validColumnType, $column, $joinedParameters);
    }

    /**
     * Generate the additional modifiers
     * 
     * @param array $modifiers
     * @return string
     */
    private function generateModifiers($modifiers) {
        $addedModifiers = [];
        foreach($modifiers as $modifier) {
            $modifierName   = head($modifier);
            $modifierParams = array_slice($modifier, 1);
            $params         = Parameters::getModifierParameters($modifierName);

            $extraParameters = [];
            foreach($params as $k => $param) {
                if(
                    is_string($param) &&
                    ($param == '' || $param == 'bool') && 
                    !isset($modifierParams[$k])
                ) {
                    throw new \Exception("Modifier {$modifierName} needs a default value.");
                }

                if(is_bool($param)) {
                    if(!isset($modifierParams[$k])) {
                        $extraParameters[] = $param ? "true" : "false";
                    } else {
                        $extraParameters[] = $modifierParams[$k];
                    }
                }
                
                if(is_string($param)) {
                    if($param == '') {
                        $extraParameters[] = "'{$modifierParams[$k]}'";
                    } else if($param == 'bool') {
                        if($modifierParams[$k] == 'true' || $modifierParams[$k] == 'false') {
                            $extraParameters[] = $modifierParams[$k];
                        } else {
                            $extraParameters[] = "'{$modifierParams[$k]}'";
                        }
                    } else {
                        $extraParameters[] = isset($modifierParams[$k]) ? "'{$modifierParams[$k]}'" : "'$param'";
                    } 
                }
            }

            $joinedExtraParameters = $this->joinParameters($extraParameters);

            $addedModifiers[] = sprintf('->%s(%s)', $modifierName, $joinedExtraParameters);
        }

        return implode('', $addedModifiers);
    }

    /**
     * Checks if the column type supplied is valid/permitted
     */
    private function checkIfValidColumnType(String $type) {
        if(!Parameters::validate($type)) {
            throw new \Exception("Invalid column type supplied: {$type}");
        }
    }

    /**
     * Joins parameters into a proper string
     * 
     * @param array $parameters
     * @return string
     */
    private function joinParameters($parameters) {
        return !empty($parameters) ? 
            implode(', ', $parameters) :
            '';
    }
}
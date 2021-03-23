<?php

namespace Andyabih\JsonToLaravelMigrations\Creators;

use Andyabih\JsonToLaravelMigrations\Traits\HasBackpackFunctions;

class BackpackControllerCreator
{
    use HasBackpackFunctions;
    protected $modelName, $controllers;
    protected $allowedOperations = ['list', 'create'];

    public function __construct($controllers)
    {
        $this->controllers = $controllers;
    }

    public function create()
    {
        if (!file_exists($this->getLocation())) {
            mkdir($this->getLocation(), 0775);
        }

        foreach ($this->controllers as $table => $controller) {
            $modelName = $this->createController($table, $controller);
            $routeName = $this->addRoute($modelName);
            $this->addToSidebar($modelName, $routeName);
        }
    }


    /**
     * @param string $table
     * @param array $controller
     * @return string $modelName
     * */
    protected function createController($table, $controller): string
    {
        $modelName = $this->generateModelName($table);
        $filename = $this->generateFileName($modelName);
        $stub     = $this->createStub($modelName, $controller);
        $path     = $this->getPath($filename);

        file_put_contents($path, $stub);
        return $modelName;
    }

    private function generateModelName($table)
    {
        return ucfirst(\Str::singular($table));
    }

    private function generateFileName($modelName)
    {
        return sprintf('%sCrudController.php', $modelName);
    }

    private function createStub($modelName, $controller)
    {
        $stub = $this->getStub();
        $stub = str_replace("{{modelName}}", $modelName, $stub);
        $stub = $this->getFields($controller, $stub);
        // $stub = str_replace("{{listOperationFields}}", $this->generateOperations($controller), $stub);
        // $stub = str_replace("{{createOperationFields}}", $this->generateOperations($controller), $stub);
        return $stub;
    }
    private function getFields($controller, $stub)
    {
        $fields = [];
        foreach ($controller as $field => $parameters) {
            if (is_array($parameters)) {
                array_push($fields, $this->getDescriptiveField($field, $parameters));
            } else {
                array_push($fields, $this->getShorthandField($field, $parameters));
            }
        }

        foreach ($this->allowedOperations as $operation) {
            $stub = str_replace("{{{$operation}OperationFields}}", $this->generateOperations(\Arr::pluck($fields, $operation)), $stub);
        }

        return $stub;
    }

    private function getShorthandField($field, $parameters)
    {
        return array(
            "list" => array("name" => $field, "type" => $parameters),
            "create" => array("name" => $field, "type" => $parameters),
        );
    }

    private function getDescriptiveField($field, $parameters)
    {
        $fields = [];
        //get field details for each operation
        foreach ($this->allowedOperations as $operation) {
            if (isset($parameters[$operation])) {
                $parameters[$operation]['name'] = $field;
                $fields[$operation] = $parameters[$operation];
            }
        }
        //if only one operation is specified, generalize to the other operations
        
        // $parameters['list'] = $parameters['list'] ?? $parameters['create'];
        // $parameters['create'] = $parameters['create'] ?? $parameters['list'];

        if (sizeof($fields) === 1) {
            $unspecified = \Arr::except($this->allowedOperations, [head(array_keys($fields))]);

            foreach ($unspecified as $missing) {
                $fields[$missing] = head($fields);
            }
        }
        return $fields;
    }



    private function generateOperations($fields)
    {
        $finalFields = [];

        foreach ($fields as $field) {

            $finalField = [];

            foreach ($field as $key => $value) {
                $finalField[] = "\t'{$key}' => " . "'{$value}'";
            }

            array_push($finalFields, "\n\t\t\t[\n\t\t\t" . implode(",\n\t\t\t", $finalField) . "\n\t\t\t]");
        }

        return implode(",\n\t\t\t", $finalFields);
    }

    private function getStub()
    {
        return file_get_contents(__DIR__ . './../stubs/crud-controller.stub');
    }

    private function getPath($filename)
    {
        return base_path() . '/app/Http/Controllers/Admin/' . $filename;
    }

    private function getLocation()
    {
        return base_path() . '/app/Http/Controllers/Admin';
    }
}

<?php

namespace Andyabih\JsonToLaravelMigrations\Creators;

class RequestCreator
{
    protected $modelName, $requests;

    public function __construct($requests)
    {
        $this->requests = $requests;
    }

    public function create()
    {
        foreach ($this->requests as $table => $rules) {
            $this->createRequest($table, $rules);
            sleep(1);
        }
    }

    protected function createRequest($table, $rules)
    {
        $modelName = $this->generateModelName($table);
        $filename = $this->generateFileName($modelName);
        $stub     = $this->createStub($modelName, $rules);
        $path     = $this->getPath($filename);

        file_put_contents($path, $stub);
    }

    private function generateModelName($table)
    {
        return ucfirst(\Str::singular($table));
    }

    private function generateFileName($modelName)
    {
        return sprintf('%sRequest.php', $modelName);
    }

    private function createStub($modelName, $rules) {
        $stub = $this->getStub();
        $stub = str_replace("{{modelName}}", $modelName, $stub);
        $stub = str_replace("{{validationRules}}", $this->stringifyRules($rules), $stub);
        return $stub;
    }

    private function stringifyRules ($rules) {
        $rulesStr = '';
        foreach($rules as $key => $rule){
            $rulesStr .= "'" . $key . "' => '" . $rule . "', \n\t\t\t" ;  
        }
        return $rulesStr;
    }

    private function getStub()
    {
        return file_get_contents(__DIR__ . './../stubs/migration.stub');
    }

    private function getPath($filename) {
        return base_path() . '/app/Http/Requests/' . $filename;
    }
}

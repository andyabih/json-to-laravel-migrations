<?php

namespace Andyabih\JsonToLaravelMigrations\Creators;

use Error;

class BackpackControllerCreator
{
    protected $modelName, $controllers;

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
            $this->createController($table, $controller);
            $this->addRoute();
        }
    }


    /** 
     * @TODO: add side bar content
     * Steal backpack/crud/src/console/commands/AddCustommRouteContent.php
     * @TODO: add to backpack custom routes 
     * Steal backpack/crud/src/console/commands/AddSidebarContent.php
     * */
    protected function createController($table, $controller)
    {
        $modelName = $this->generateModelName($table);
        $filename = $this->generateFileName($modelName);
        $stub     = $this->createStub($modelName, $controller);
        $path     = $this->getPath($filename);

        file_put_contents($path, $stub);
    }

    private function addRoute()
    {
        $path = 'routes/backpack/custom.php';
        $disk_name = config('backpack.base.root_disk_name');
        $disk = \Storage::disk($disk_name);
        $code = 'test';
        // Route::crud('category', 'CategoryCrudController')
        if ($disk->exists($path)) {
            $old_file_path = $disk->path($path);

            // insert the given code before the file's last line
            $file_lines = file($old_file_path, FILE_IGNORE_NEW_LINES);

            $end_line_number = $this->customRoutesFileEndLine($file_lines);
            $file_lines[$end_line_number + 1] = $file_lines[$end_line_number];
            $file_lines[$end_line_number] = '    ' . $code;
            $new_file_content = implode(PHP_EOL, $file_lines);

            if (!$disk->put($path, $new_file_content)) {
                throw new Error('Could not write to file: ' . $path);
            }
        }
    }

    private function customRoutesFileEndLine($file_lines)
    {
        // in case the last line has not been modified at all
        $end_line_number = array_search('}); // this should be the absolute last line of this file', $file_lines);

        if ($end_line_number) {
            return $end_line_number;
        }

        // otherwise, in case the last line HAS been modified
        // return the last line that has an ending in it
        $possible_end_lines = array_filter($file_lines, function ($k) {
            return strpos($k, '});') === 0;
        });

        if ($possible_end_lines) {
            end($possible_end_lines);
            $end_line_number = key($possible_end_lines);

            return $end_line_number;
        }
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
        $stub = str_replace("{{listOperationFields}}", $this->generateOperations($controller), $stub);
        $stub = str_replace("{{createOperationFields}}", $this->generateOperations($controller), $stub);
        return $stub;
    }

    private function generateOperations($controller)
    {

        $finalFields = [];

        foreach ($controller as $column => $fields) {
            $fields['name'] = $column;

            $finalField = [];

            foreach ($fields as $key => $value) {
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

    /** 
     * 
     * 
     * 
     * BACKPACK STUFF 
     * 
     * 
     * 
     * */

    // protected function getPath($name)
    // {
    //     $name = str_replace($this->laravel->getNamespace(), '', $name);

    //     return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'CrudController.php';
    // }


    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    // protected function getDefaultNamespace($rootNamespace)
    // {
    //     return $rootNamespace.'\Http\Controllers\Admin';
    // }



    // protected function getAttributes($model)
    // {
    //     $attributes = [];
    //     $model = new $model;

    //     // if fillable was defined, use that as the attributes
    //     if (count($model->getFillable())) {
    //         $attributes = $model->getFillable();
    //     } else {
    //         // otherwise, if guarded is used, just pick up the columns straight from the bd table
    //         $attributes = \Schema::getColumnListing($model->getTable());
    //     }

    //     return $attributes;
    // }

    /**
     * Replace the table name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    // protected function replaceSetFromDb(&$stub, $name)
    // {
    //     $class = Str::afterLast($name, '\\');
    //     $model = "App\\Models\\$class";

    //     if (! class_exists($model)) {
    //         return $this;
    //     }

    //     $attributes = $this->getAttributes($model);

    //     // create an array with the needed code for defining fields
    //     $fields = Arr::except($attributes, ['id', 'created_at', 'updated_at', 'deleted_at']);
    //     $fields = collect($fields)
    //         ->map(function ($field) {
    //             return "CRUD::field('$field');";
    //         })
    //         ->toArray();

    //     // create an array with the needed code for defining columns
    //     $columns = Arr::except($attributes, ['id']);
    //     $columns = collect($columns)
    //         ->map(function ($column) {
    //             return "CRUD::column('$column');";
    //         })
    //         ->toArray();

    //     // replace setFromDb with actual fields and columns
    //     $stub = str_replace('CRUD::setFromDb(); // fields', implode(PHP_EOL.'        ', $fields), $stub);
    //     $stub = str_replace('CRUD::setFromDb(); // columns', implode(PHP_EOL.'        ', $columns), $stub);

    //     return $this;
    // }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     *
     * @return string
     */
    // protected function replaceModel(&$stub, $name)
    // {
    //     $class = str_replace($this->getNamespace($name).'\\', '', $name);
    //     $stub = str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);

    //     return $this;
    // }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @return string
     */
    // protected function buildClass($name)
    // {
    //     $stub = $this->files->get($this->getStub());

    //     $this->replaceNamespace($stub, $name)
    //             ->replaceNameStrings($stub, $name)
    //             ->replaceModel($stub, $name)
    //             ->replaceSetFromDb($stub, $name);

    //     return $stub;
    // }


}

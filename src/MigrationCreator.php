<?php

namespace Andyabih\JsonToLaravelMigrations;

class MigrationCreator {
    /**
     * Migration methods
     */
    protected $methods;

    /**
     * Create an instance of the Migration Creator
     * 
     * @param array $methods
     * @return void
     */
    public function __construct(Array $methods) {
        $this->methods = $methods;
    }

    public function create() {
        foreach($this->methods as $table => $methods) {
            $this->createMigration($table, $methods);
        }
    }

    private function createMigration($table, $methods) {
        $filename = $this->generateFileName($table);
        $name     = $this->generateName($table);
        $stub     = $this->createStub($name, $table, $methods);
        $path     = $this->getPath($filename);
        
        file_put_contents($path, $stub);
    }

    private function generateName($table) {
        return \Str::studly(
            sprintf("create_%s_table", strtolower($table))
        );
    }

    private function generateFileName($table) {
        return sprintf('%s_create_%s_table.php', date('Y_m_d_His'), strtolower($table));
    }

    private function createStub($className, $tableName, $methods) {
        $stub = $this->getStub();
        $stub = str_replace("{{migrationName}}", $className, $stub);
        $stub = str_replace("{{tableName}}", $tableName, $stub);
        $stub = str_replace("{{methods}}", implode("\n\t\t\t", $methods), $stub);
        return $stub;
    }

    private function getStub() {
        return file_get_contents(__DIR__ . '/stubs/migration.stub');
    }

    private function getPath($filename) {
        return base_path() . '/database/migrations/' . $filename;
    }
}
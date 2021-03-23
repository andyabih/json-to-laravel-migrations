<?php 
namespace Andyabih\JsonToLaravelMigrations\Traits;

trait HasParameterChecks {
    public function wantsParameter($parameters): bool {
        return isset($parameters[$this->key]);
    }
}
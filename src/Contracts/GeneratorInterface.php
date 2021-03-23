<?php 
namespace Andyabih\JsonToLaravelMigrations\Contracts;
/** 
 * @method parse , must return $this to allow method chaining
 * @method create, called after the @method parse to generate the needed files
 */
Interface GeneratorInterface {
    public function parse () ;
    public function create(): void;
}

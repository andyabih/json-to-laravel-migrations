<?php

namespace Andyabih\JsonToLaravelMigrations;

abstract class Parameters {
    /**
     * The available column types to use and default
     * values of parameters
     * 
     * @var array[]
     */
    static $columnTypes = [
        'bigIncrements'         => [],
        'bigInteger'            => [],
        'binary'                => [],
        'boolean'               => [],
        'char'                  => [100],
        'dateTimeTz'            => [0],
        'dateTime'              => [0],
        'date'                  => [],
        'decimal'               => [8, 2],
        'double'                => [8, 2],
        'enum'                  => [[]],
        'float'                 => [8, 2],
        'foreignId'             => [],
        'geometryCollection'    => [],
        'geometry'              => [],
        'increments'            => [],
        'integer'               => [],
        'ipAddress'             => [],
        'json'                  => [],
        'jsonb'                 => [],
        'lineString'            => [],
        'longText'              => [],
        'macAddress'            => [],
        'mediumIncrements'      => [],
        'mediumInteger'         => [],
        'mediumText'            => [],
        'morphs'                => [],
        'multiLineString'       => [],
        'multiPoint'            => [],
        'multiPolygon'          => [],
        'nullableMorphs'        => [],
        'nullableUuidMorphs'    => [],
        'point'                 => [],
        'polygon'               => [],
        'set'                   => [[]],
        'smallIncrements'       => [],
        'smallInteger'          => [],
        'softDeletesTz'         => [0],
        'softDeletes'           => [0],
        'string'                => [100],
        'text'                  => [],
        'timeTz'                => [0],
        'time'                  => [0],
        'timestampTz'           => [0],
        'timestamp'             => [0],
        'timestamps'            => [0],
        'tinyIncrements'        => [],
        'tinyInteger'           => [],
        'unsignedBigInteger'    => [],
        'unsignedDecimal'       => [8, 2],
        'unsignedInteger'       => [],
        'unsignedMediumInteger' => [],
        'unsignedSmallInteger'  => [],
        'unsignedTinyInteger'   => [],
        'uuidMorphs'            => [],
        'uuid'                  => [],
        'year'                  => [],
    ];
    
    /**
     * List of aliases for column types
     * 
     * @var string[]
     */
    static $aliases = [
        'foreign' => 'foreignId'
    ];

    /**
     * List of modifiers
     * 
     * @var string[]
     */
    static $modifiers = [
        'after'              => [''],
        'autoIncrement'      => [],
        'charset'            => ['utf8mb4'],
        'collation'          => ['utf8mb4_unicode_ci'],
        'comment'            => [''],
        'default'            => ['bool'],
        'first'              => [],
        'from'               => [1],
        'nullable'           => [true],
        'storedAs'           => [''],
        'unsigned'           => [],
        'useCurrent'         => [],
        'useCurrentOnUpdate' => [],
        'virtualAs'          => [''],
        'generatedAs'        => [''],
        'index'              => [],
        'unique'             => [],
        'always'             => [],
        'constrained'        => [],
        'onDelete'           => ['cascade']
    ];

    /**
     * Returns a proper cased column type
     * 
     * @param string $columnType
     * @return string
     */
    public static function getValidColumnType($columnType) {
        $lowercaseType  = strtolower($columnType);
        $types          = array_keys(self::$columnTypes);
        $lowercaseTypes = array_map('strtolower', $types);

        $index          = array_search($lowercaseType, $lowercaseTypes);

        // It doesn't exist, then check if it's an alias
        if(!$index) {
            if(!isset(self::$aliases[$lowercaseType])) {
                throw new \Exception("Invalid column type {$columnType}");
            }

            return self::$aliases[$lowercaseType];
        }
        
        return $types[$index];
    }

    /**
     * Returns the parameters for a specific column type
     * 
     * @param string $columnType
     * @return array
     */
    public static function getParameters($columnType) {
        return self::$columnTypes[$columnType];
    }
    
    /**
     * Returns the modifier parameters
     * 
     * @param string $modifier
     * @return array
     */
    public static function getModifierParameters($modifier) {
        return self::$modifiers[$modifier];
    }

    /**
     * Checks if the column type exists
     * 
     * @param string $type
     * @return bool
     */
    public static function validate(String $type) {
        $lowercaseType = strtolower($type);

        return in_array($lowercaseType, self::normalizedTypes()) ||
            in_array($lowercaseType, self::normalizedAliases());
    }

    /**
     * Returns normalized column types
     * 
     * @return array
     */
    private static function normalizedTypes() {
        return Helpers::normalize(array_keys(self::$columnTypes));
    }

    /**
     * Returns normalized aliases
     * 
     * @return array
     */
    private static function normalizedAliases() {
        return Helpers::normalize(array_keys(self::$aliases));
    }
}
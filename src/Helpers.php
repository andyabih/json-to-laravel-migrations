<?php

namespace Andyabih\JsonToLaravelMigrations;

class Helpers {
    /**
     * Returns an array with the same keys and 
     * a default value
     * 
     * @param array $ar
     * @return array
     */
    public static function justKeys(Array $ar, $fillWith = []) {
        return array_fill_keys(array_keys($ar), $fillWith);
    }

    /**
     * Makes an array lowercase
     * 
     * @return array
     */
    public static function normalize(Array $ar) {
        return array_map('strtolower', $ar);
    }
}
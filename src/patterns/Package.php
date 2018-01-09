<?php

/**
 * Registry class
 */
class Package
{
    protected static $data = [];

    public static function set($key, $value) {
        self::$data[$key] = $value;
    }

    public static function get($key) {
        return isset(self::$data[$key]) ? self::$data[$key] : null;
    }

    final public static function removeObject($key) {
        if (array_key_exists($key, self::$data)) {
            unset(self::$data[$key]);
        }
    }
}

Package::set('name', 'Package name');
var_dump(Package::get('name'));

Package::set('name1', 'Package12 name');
var_dump(Package::get('name1'));

<?php

abstract class FactoryAbstract
{
    protected static $instances = [];

    public static function getInstance() {
        $className = static::getClassName();
        if (!isset(self::$instances[$className]) || !(self::$instances[$className] instanceof $className)) {
            self::$instances[$className] = new $className();
        }
        return self::$instances[$className];
    }

    public static function removeInstance() {
        $className = static::getClassName();
        if (array_key_exists($className, self::$instances)) {
            unset(self::$instances[$className]);
        }
    }

    final protected static function getClassName() {
        return get_called_class();
    }

    protected function __construct() {
    }

    final protected function __clone() {
    }
}

abstract class Factory extends FactoryAbstract
{
    final public static function getInstance() {
        return parent::getInstance();
    }

    final public static function removeInstance() {
        parent::removeInstance();
    }
}

// using:
class FirstProduct extends Factory
{
    public $a = [];
}

class SecondProduct extends FirstProduct
{
}

FirstProduct::getInstance()->a[] = 1;
FirstProduct::getInstance()->a[] = 3;
SecondProduct::getInstance()->a[] = 2;
SecondProduct::getInstance()->a[] = 4;

var_dump(FirstProduct::getInstance()->a);
var_dump(SecondProduct::getInstance()->a);
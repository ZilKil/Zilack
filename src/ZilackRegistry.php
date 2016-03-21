<?php
namespace Zilack;

class ZilackRegistry
{
    private static $store = [];

    public static function add($object, $name = null) {
        if(empty($name)) {
            throw new \Exception('You must pass in a name to store an item in the registry.');
        }
        self::$store[$name] = $object;
    }

    public static function get($name) {
        if(!self::$store[$name]) {
            return null;
        } else {
            return self::$store[$name];
        }
    }

    public static function contains($name) {
        if(isset(self::$store[$name])) {
            return true;
        }
        return false;
    }

    public static function remove($name) {
        if(self::contains($name)) {
            unset(self::$store[$name]);
        }
    }
}
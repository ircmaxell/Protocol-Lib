<?php

namespace ProtocolLib;

class ProtocolHelper {
    protected static $cache = array();

    public static function getProtocol($interface) {
        if (!isset(static::$cache[$interface])) {
            static::$cache[$interface] = new ProtocolWrapper($interface);
        }
        return static::$cache[$interface];
    }

    public static function getMethodProtocol($interface) {
        if (!isset(static::$cache[$interface])) {
            static::$cache[$interface] = new ProtocolMethodWrapper($interface);
        }
        return static::$cache[$interface];
    }

    public static function doesImplement($obj, $interface) {
        return static::getProtocol($interface)->doesImplement($obj);
    }

    public static function doesMethodImplement($method, $interface) {
        return static::getMethodProtocol($interface)->doesMethodImplement($method);
    }

    public static function doesFunctionImplement($function, $interface) {
        return static::getMethodProtocol($interface)->doesFunctionImplement($function);
    }

}

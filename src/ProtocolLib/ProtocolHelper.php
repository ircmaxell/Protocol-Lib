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

    public static function doesImplement($obj, $interface) {
        return static::getProtocol($interface)->doesImplement($obj);
    }

}
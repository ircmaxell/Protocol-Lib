<?php

namespace ProtocolLib;

use ReflectionMethod as RM;

class ProtocolWrapper {

    protected $reflector;

    public function __construct($interface) {
        if (!interface_exists($interface)) {
            throw new \LogicException("Requested Interface Not Defined: $interface");
        }
        $this->reflector = new \ReflectionClass($interface);
    }

    public function doesImplement($obj) {
        if (!is_object($obj)) {
            return false;
        }
        $reflector = new \ReflectionObject($obj);
        foreach ($this->reflector->getMethods() as $method) {
            if (!$this->checkMethod($method, $reflector)) {
                return false;
            }
        }
        return true;
    }

    protected function checkMethod(\ReflectionMethod $method, \ReflectionClass $class) {
        if (!$class->hasMethod($method->name)) {
            return false;
        }
        $otherMethod = $class->getMethod($method->name);
        foreach (array(
            RM::IS_STATIC,
            RM::IS_PUBLIC,
            RM::IS_PROTECTED,
            ) as $modifier) {
            if (($method->getModifiers() & $modifier) !== ($otherMethod->getModifiers() & $modifier)) {
                return false;
            }
        }
        if ($method->getNumberOfParameters() !== $otherMethod->getNumberOfParameters()) {
            return false;
        }
        $otherParams = $otherMethod->getParameters();
        foreach ($method->getParameters() as $key => $param) {
            if (!$this->checkParam($param, $otherParams[$key])) {
                return false;
            }
        }
        return true;
    }

    protected function checkParam($param1, $param2) {
        foreach (array(
            'isArray',
            'isCallable',
            'isDefaultValueAvailable',
            'isDefaultValueConstant',
            'isOptional',
            'isPassedByReference',
            'canBePassedByValue',
            'getDefaultValue',
            'getDefaultValueConstantName',
            'allowsNull',
            'getClass',
            ) as $check) {
            if ($param1->$check() !== $param2->$check()) {
                return false;
            }
        }
        return true;
    }
}
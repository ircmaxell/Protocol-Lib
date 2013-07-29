<?php

namespace ProtocolLib;

class ProtocolWrapper {

    protected $reflector;

    public function __construct($interface) {
        if (!$interface instanceof \ReflectionClass) {
            try {
                $interface = new \ReflectionClass($interface);
            } catch (\ReflectionException $e) {
                throw new LogicException(sprintf(
                    "Requested Interface Not Defined: %s",
                    $interface
                ), null, $e);
            }
        }
        if (!$interface->isInterface()) {
            throw new LogicException(sprintf(
                "Requested Interface Is Not An Interface: %s",
                $interface->getName()
            ));
        }
        $this->reflector = $interface;
    }

    public function doesImplement($class) {
        if (!$class instanceof \ReflectionClass) {
            try {
                $class = new \ReflectionClass($class);
            } catch (\ReflectionException $e) {
                return false;
            }
        }
        foreach ($this->reflector->getMethods() as $method) {
            if (!$class->hasMethod($method->name)) {
                return false;
            }
            $otherMethod = $class->getMethod($method->name);
            $methodProtocol = $this->getMethodProtocol($method->name);
            if (!$methodProtocol->doesMethodImplement($otherMethod)) {
                return false;
            }
        }
        return true;
    }

    public function getMethodProtocol($name)
    {
        return new ProtocolMethodWrapper($this->reflector->getMethod($name));
    }
}

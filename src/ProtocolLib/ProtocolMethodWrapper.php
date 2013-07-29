<?php

namespace ProtocolLib;

class ProtocolMethodWrapper {

    protected $reflector;

    public function __construct($method) {
        if (!$method instanceof \ReflectionMethod) {
            try {
                $method = new \ReflectionMethod($method);
            } catch (\ReflectionException $e) {
                throw new LogicException(sprintf(
                    "Requested Interface Method Not Defined: %s",
                    $method
                ), null, $e);
            }
        }
        if (!$method->getDeclaringClass()->isInterface()) {
            throw new LogicException(sprintf(
                "Declaring Class Of Requested Interface Method Is Not An Interface: %s",
                $method->getDeclaringClass()->getName()
            ));
        }
        $this->reflector = $method;
    }

    public function doesMethodImplement($method) {
        if (!$method instanceof \ReflectionMethod) {
            try {
                if (is_array($method)) {
                    $method = new \ReflectionMethod($method[0], $method[1]);
                } else {
                    $method = new \ReflectionMethod($method);
                }
            } catch (\ReflectionException $e) {
                return false;
            }
        }
        if ($this->reflector->name !== $method->name) {
            return false;
        }
        $methodModifiers = $method->getModifiers();
        $reflModifiers = $this->reflector->getModifiers();
        foreach (array(
            \ReflectionMethod::IS_STATIC,
            \ReflectionMethod::IS_PUBLIC,
            \ReflectionMethod::IS_PROTECTED,
            ) as $modifier) {
            if (($methodModifiers & $modifier) !== ($reflModifiers & $modifier)) {
                return false;
            }
        }
        return $this->checkParams($method);
    }

    public function doesFunctionImplement($function) {
        if (!$function instanceof \ReflectionFunction) {
            try {
                $function = new \ReflectionFunction($function);
            } catch (\ReflectionException $e) {
                return false;
            }
        }
        return $this->checkParams($function);
    }

    protected function checkParams(\ReflectionFunctionAbstract $function) {
        if ($function->getNumberOfParameters() !== $function->getNumberOfParameters()) {
            return false;
        }
        $params = $this->reflector->getParameters();
        foreach ($function->getParameters() as $key => $param) {
            if (!$this->checkParam($param, $params[$key])) {
                return false;
            }
        }
        return true;
    }

    protected function checkParam(\ReflectionParameter $param1, \ReflectionParameter $param2) {
        if ($param1->isDefaultValueAvailable() !== $param2->isDefaultValueAvailable()) {
            return false;
        }
        // Only check if a default value is available, otherwise it throws an
        // exception (ReflectionException: Failed to retrieve the default value).
        // http://php.net/reflectionparameter.getdefaultvalue.php#refsect1-reflectionparameter.getdefaultvalue-notes
        if ($param1->isDefaultValueAvailable()) {
            if ($param1->isDefaultValueConstant() !== $param2->isDefaultValueConstant()) {
                return false;
            }
            // Should probably be omitted. Looks like default values of
            // arguments don't have to match default values of the interface
            // method. http://3v4l.org/XYT9k
            if ($param1->getDefaultValue() !== $param2->getDefaultValue()) {
                return false;
            }
            // Should be omitted for the reason above and because it returns the
            // actual declaration.
            //
            // interface InterfaceWithConstantDefaultValue {
            //     const TEST = 1;
            //     function test($param = self::TEST);
            // }
            //
            // class ClassWithConstantDefaultValue
            // {
            //     public function test($param = InterfaceWithConstantDefaultValue::TEST) {}
            // }
            //
            // This would return "self::TEST" and "TestInterfaceWithConstantDefaultValue::TEST".
            //
            /*if ($param1->isDefaultValueConstant() && $param1->getDefaultValueConstantName() !== $param2->getDefaultValueConstantName()) {
                return false;
            }*/
        }
        foreach (array(
            'isArray',
            'isCallable',
            'isOptional',
            'isPassedByReference',
            'canBePassedByValue',
            'allowsNull',
            ) as $check) {
            if ($param1->$check() !== $param2->$check()) {
                return false;
            }
        }
        // ReflectionParameter::getClass() throws an exception if the class does
        // not exist (ReflectionException: Class Foo\Bar does not exist).
        $class1 = null;
        $class2 = null;
        try {
            if ($param1->getClass()) {
                $class1 = $param1->getClass()->name;
            }
        } catch (\ReflectionException $e) {
            if (!preg_match('/Class (.+) does not exist/', $e->getMessage(), $matches)) {
                throw $e;
            }
            $class1 = $matches[1];
        }
        try {
            if ($param2->getClass()) {
                $class2 = $param2->getClass()->name;
            }
        } catch (\ReflectionException $e) {
            if (!preg_match('/Class (.+) does not exist/', $e->getMessage(), $matches)) {
                throw $e;
            }
            $class2 = $matches[1];
        }
        if ($class1 !== $class2) {
            return false;
        }
        return true;
    }
}

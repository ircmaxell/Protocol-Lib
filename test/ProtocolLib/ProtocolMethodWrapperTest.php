<?php

namespace ProtocolLib;

class ProtocolMethodWrapperTest extends \PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $obj = new ProtocolMethodWrapper('Iterator::current');
    }

    /**
     * @expectedException ProtocolLib\LogicException
     * @expectedExceptionMessage Requested Interface Method Not Defined: NonExtistingClass
     */
    public function testConstructNonExistingClassFailure() {
        $obj = new ProtocolMethodWrapper('NonExtistingClass::foo');
    }

    /**
     * @expectedException ProtocolLib\LogicException
     * @expectedExceptionMessage Declaring Class Of Requested Interface Method Is Not An Interface: ProtocolLib\ProtocolMethodWrapperTest
     */
    public function testConstructNonInterfaceFailure() {
        $obj = new ProtocolMethodWrapper('ProtocolLib\ProtocolMethodWrapperTest::testConstructNonInterfaceFailure');
    }

    public function testMethodImplementArguments() {
        $protocol = new ProtocolMethodWrapper('ProtocolLib\TestInterfaceWithInternalClassTypehint::test');

        $this->assertTrue($protocol->doesMethodImplement('ProtocolLib\TestClassWithInternalClassTypehint::test'));
        $this->assertTrue($protocol->doesMethodImplement(array('ProtocolLib\TestClassWithInternalClassTypehint', 'test')));
        $this->assertTrue($protocol->doesMethodImplement(array(new TestClassWithInternalClassTypehint, 'test')));
    }

    /**
     * @dataProvider methodFunctionDataProvider
     */
    public function testImplement($type, $method, $function, $closure) {
        foreach ($this->interfaceDataProvider() as $options) {
            $protocol = new ProtocolMethodWrapper($options[1]);

            $expected = $options[0] === $type ? true : false;

            $this->assertSame($expected, $protocol->doesMethodImplement($method), $options[1].' -> method');
            $this->assertSame($expected, $protocol->doesFunctionImplement($function), $options[1].' -> function');
            $this->assertSame($expected, $protocol->doesFunctionImplement($closure), $options[1].' -> closure');
        }
    }

    public function interfaceDataProvider()
    {
        return array(
            'internal class typehint' => array(
                'internal-class-typehint',
                __NAMESPACE__.'\TestInterfaceWithInternalClassTypehint::test'
            ),
            'existing class typehint' => array(
                'exisiting-class-typehint',
                __NAMESPACE__.'\TestInterfaceWithExisitingClassTypehint::test'
            ),
            'non-existing class typehint' => array(
                'non-exisiting-class-typehint',
                __NAMESPACE__.'\TestInterfaceWithNonExisitingClassTypehint::test'
            ),
            'callable typehint' => array(
                'callable-typehint',
                __NAMESPACE__.'\TestInterfaceWithCallableTypehint::test'
            ),
            'array typehint' => array(
                'array-typehint',
                __NAMESPACE__.'\TestInterfaceWithArrayTypehint::test'
            ),
            'without typehint' => array(
                'without-typehint',
                __NAMESPACE__.'\TestInterfaceWithoutTypehint::test'
            ),
            'with null default value' => array(
                'with-null-default-value',
                __NAMESPACE__.'\TestInterfaceWithNullDefaultValue::test'
            ),
            'with internal constant default value' => array(
                'with-internal-constant-default-value',
                __NAMESPACE__.'\TestInterfaceWithInternalConstantDefaultValue::test'
            ),
            'with constant default value' => array(
                'with-constant-default-value',
                __NAMESPACE__.'\TestInterfaceWithConstantDefaultValue::test'
            ),
        );
    }

    public function methodFunctionDataProvider()
    {
        return array(
            'internal class typehint' => array(
                'internal-class-typehint',
                __NAMESPACE__.'\TestClassWithInternalClassTypehint::test',
                __NAMESPACE__.'\TestFunctionWithInternalClassTypehint',
                function (\Traversable $param) {}
            ),
            'existing class typehint' => array(
                'exisiting-class-typehint',
                __NAMESPACE__.'\TestClassWithExisitingClassTypehint::test',
                __NAMESPACE__.'\TestFunctionWithExisitingClassTypehint',
                function (\stdClass $param) {}
            ),
            'non-existing class typehint' => array(
                'non-exisiting-class-typehint',
                __NAMESPACE__.'\TestClassWithNonExisitingClassTypehint::test',
                __NAMESPACE__.'\TestFunctionWithNonExisitingClassTypehint',
                function (NonExisitingClass $param) {}
            ),
            'callable typehint' => array(
                'callable-typehint',
                __NAMESPACE__.'\TestClassWithCallableTypehint::test',
                __NAMESPACE__.'\TestFunctionWithCallableTypehint',
                function (callable $param) {}
            ),
            'array typehint' => array(
                'array-typehint',
                __NAMESPACE__.'\TestClassWithArrayTypehint::test',
                __NAMESPACE__.'\TestFunctionWithArrayTypehint',
                function (array $param) {}
            ),
            'without typehint' => array(
                'without-typehint',
                __NAMESPACE__.'\TestClassWithoutTypehint::test',
                __NAMESPACE__.'\TestFunctionWithoutTypehint',
                function ($param) {}
            ),
            'with null default value' => array(
                'with-null-default-value',
                __NAMESPACE__.'\TestClassWithNullDefaultValue::test',
                __NAMESPACE__.'\TestFunctionWithNullDefaultValue',
                function ($param = null) {}
            ),
            'with internal constant default value' => array(
                'with-internal-constant-default-value',
                __NAMESPACE__.'\TestClassWithInternalConstantDefaultValue::test',
                __NAMESPACE__.'\TestFunctionWithInternalConstantDefaultValue',
                function ($param = \DIRECTORY_SEPARATOR) {}
            ),
            'with constant default value' => array(
                'with-constant-default-value',
                __NAMESPACE__.'\TestClassWithConstantDefaultValue::test',
                __NAMESPACE__.'\TestFunctionWithConstantDefaultValue',
                function ($param = TestInterfaceWithConstantDefaultValue::TEST) {}
            ),
        );
    }
}

interface TestInterfaceWithInternalClassTypehint
{
    function test(\Traversable $param);
}

interface TestInterfaceWithExisitingClassTypehint
{
    function test(\stdClass $param);
}

interface TestInterfaceWithNonExisitingClassTypehint
{
    function test(NonExisitingClass $param);
}

interface TestInterfaceWithCallableTypehint
{
    function test(callable $param);
}

interface TestInterfaceWithArrayTypehint
{
    function test(array $param);
}

interface TestInterfaceWithoutTypehint
{
    function test($param);
}

interface TestInterfaceWithNullDefaultValue
{
    function test($param = null);
}

interface TestInterfaceWithInternalConstantDefaultValue
{
    function test($param = \DIRECTORY_SEPARATOR);
}

interface TestInterfaceWithConstantDefaultValue
{
    const TEST = 1;
    function test($param = self::TEST);
}

// ---

class TestClassWithInternalClassTypehint
{
    public function test(\Traversable $param) {}
}

class TestClassWithExisitingClassTypehint
{
    public function test(\stdClass $param) {}
}

class TestClassWithNonExisitingClassTypehint
{
    public function test(NonExisitingClass $param) {}
}

class TestClassWithCallableTypehint
{
    public function test(callable $param) {}
}

class TestClassWithArrayTypehint
{
    public function test(array $param) {}
}

class TestClassWithoutTypehint
{
    public function test($param) {}
}

class TestClassWithNullDefaultValue
{
    public function test($param = null) {}
}

class TestClassWithInternalConstantDefaultValue
{
    public function test($param = \DIRECTORY_SEPARATOR) {}
}

class TestClassWithConstantDefaultValue
{
    public function test($param = TestInterfaceWithConstantDefaultValue::TEST) {}
}

// ---

function TestFunctionWithInternalClassTypehint(\Traversable $param) {}

function TestFunctionWithExisitingClassTypehint(\stdClass $param) {}

function TestFunctionWithNonExisitingClassTypehint(NonExisitingClass $param) {}

function TestFunctionWithCallableTypehint(callable $param) {}

function TestFunctionWithArrayTypehint(array $param) {}

function TestFunctionWithoutTypehint($param) {}

function TestFunctionWithNullDefaultValue($param = null) {}

function TestFunctionWithInternalConstantDefaultValue($param = \DIRECTORY_SEPARATOR) {}

function TestFunctionWithConstantDefaultValue($param = TestInterfaceWithConstantDefaultValue::TEST) {}

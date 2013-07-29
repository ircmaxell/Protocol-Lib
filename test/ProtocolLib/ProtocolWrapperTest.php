<?php

namespace ProtocolLib;

class ProtocolWrapperTest extends \PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $obj = new ProtocolWrapper('Iterator');
    }

    /**
     * @expectedException ProtocolLib\LogicException
     * @expectedExceptionMessage Requested Interface Not Defined: NonExtistingClass
     */
    public function testConstructNonExistingClassFailure() {
        $obj = new ProtocolWrapper('NonExtistingClass');
    }

    /**
     * @expectedException ProtocolLib\LogicException
     * @expectedExceptionMessage Requested Interface Is Not An Interface: ProtocolLib\ProtocolWrapperTest
     */
    public function testConstructNonInterfaceFailure() {
        $obj = new ProtocolWrapper('ProtocolLib\ProtocolWrapperTest');
    }

    public function testDoesImplement() {
        $obj = new ProtocolWrapper("IteratorAggregate");
        $ao = new \ArrayObject(array());
        $this->assertTrue($obj->doesImplement($ao));
    }

    public function testDoesNotImplement() {
        $obj = new ProtocolWrapper("Iterator");
        $ao = new \StdClass;
        $this->assertFalse($obj->doesImplement($ao));
    }

    public function testGetMethodProtocolReturnsMethodWrapper() {
        $obj = new ProtocolWrapper("IteratorAggregate");
        $this->assertTrue($obj->getMethodProtocol('getIterator')->doesMethodImplement('ArrayObject::getIterator'));
    }

}
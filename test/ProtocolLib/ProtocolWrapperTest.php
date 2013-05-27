<?php

namespace ProtocolLib;

class ProtocolWrapperTest extends \PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $obj = new ProtocolWrapper('Iterator');
    }

    /**
     * @expectedException LogicException
     */
    public function testConstructFailure() {
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

}
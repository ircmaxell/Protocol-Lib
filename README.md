# ProtocolLib

This is a simple library to allow for checking of "GO-style" interfaces in PHP.

Basically, it checks if an object "would" implement an interface (meaning all methods and signatures match) without having to actually implement it.

## Examples

    class Foo {
        public function bar($a) {}
    }

    interface Bar {
    }
    interface Bar2 {
        public function bar();
    }
    interface Bar3 {
        public function bar($a);
    }
    interface Bar4 {
        public function bar($b);
    }
    interface Bar5 {
        public function bar(a $a);
    }

When using our helper, checks would look like this:

    use ProtocolLib\ProtocolHelper;

    $foo = new Foo;

    var_dump(ProtocolHelper::doesImplement($foo, 'Bar'));
    // True - Empty interfaces *always* are implemented

    var_dump(ProtocolHelper::doesImplement($foo, 'Bar2'));
    // False - Different Number Of Parameters

    var_dump(ProtocolHelper::doesImplement($foo, 'Bar3'));
    // True

    var_dump(ProtocolHelper::doesImplement($foo, 'Bar4'));
    // True - Parameter name doesn't matter

    var_dump(ProtocolHelper::doesImplement($foo, 'Bar5'));
    // False - Typehint is different

    var_dump(ProtocolHelper::doesImplement($foo, 'Foo'));
    // LogicException Invalid Interface - Because "Foo" is not an interface

It's as simple as that!
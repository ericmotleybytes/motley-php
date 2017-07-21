<?php
use PHPUnit\Framework\Testcase;

class T00101_GenericSimpleTest extends Testcase {
    public function testPushAndPop() {
        $stack = [];
        $this->assertEquals(0, count($stack));
        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));
        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }
    public function testSimpleMath() {
        $stack = [];
        $this->assertEquals(4, 2+2);
    }
}
?>

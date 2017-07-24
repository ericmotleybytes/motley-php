<?php
/// Source code for PHPUnit::Framework::Testcase::T100_GenericSimpleTest.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;

/// Simple PHPUnit tests. Mostly just checks that the PHPUnit utility
/// is properly installed and working.
class T100_GenericSimpleTest extends Testcase {

    /// Test that array_push and array_pop work.
    public function testPushAndPop() {
        $stack = [];
        $this->assertEquals(0, count($stack));
        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));
        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }

    /// Test very simple addition.
    public function testSimpleMath() {
        $stack = [];
        $this->assertEquals(4, 2+2);
    }

}
?>

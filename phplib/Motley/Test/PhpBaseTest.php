<?php
/// Source code file for Motley::Test::PhpBaseTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;

/// Simple PHPUnit tests. Mostly just checks that the PHPUnit utility
/// is properly installed and working.
class PhpBaseTest extends Testcase {

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

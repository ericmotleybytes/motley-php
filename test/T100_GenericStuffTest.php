<?php
/// Source code for PHPUnit::Framework::Testcase::T100_GenericStuffTest.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;

/// Simple PHPUnit tests. Mostly just checks that the PHPUnit utility
/// is properly installed and working.
class T100_GenericStuffTest extends Testcase {

    /// Test that basic hash arrays work.
    public function testHashArray() {
        $hash = [];
        $this->assertEquals(0, count($hash));
        $hash["apple"] = "fruit";
        $hash["dog"]   = "animal";
        $this->assertEquals(2, count($hash));
        $this->assertEquals('fruit',  $hash["apple"]);
        $this->assertEquals('animal', $hash["dog"]);
    }

    /// Test that basic multiplication works.
    public function testDouglasAdams() {
        $stack = [];
        $this->assertEquals(42, 2*3*7);
    }

}
?>

<?php
/// Source code file for Motley::Test::PhpStuffTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;

/// Simple PHPUnit tests. Mostly just checks that the PHPUnit utility
/// is properly installed and working.
class PhpStuffTest extends Testcase {

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

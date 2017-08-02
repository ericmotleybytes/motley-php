<?php
/// Source code file for Motley::Test::CommandDoubleDashTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandDoubleDash;

/**
 * Tests the Motley::CommandDoubleDash class.
 */
class CommandDoubleDashTest extends Testcase {

    /**
     * Test instantiation.
     */
    public function testNewDoubleDash() {
        $dd1 = new CommandDoubleDash();
        $dd2 = new CommandDOubleDash();
        $this->assertFalse($dd1===$dd2);
    }

    /// Test getName, get/setDescription, and getDisplayName.
    public function testGet() {
        $dd = new CommandDoubleDash();
        $this->assertEquals("--",$dd->getName());
        $this->assertEquals("--",$dd->getDisplayName());
        $this->assertTrue(strlen($dd->getDescription())>1);
        $desc = "another description.";
        $dd->setDescription($desc);
        $this->assertEquals($desc,$dd->getDescription());
    }

}
?>

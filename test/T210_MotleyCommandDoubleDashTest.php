<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;
use Motley\CommandDoubleDash;

/**
 * Tests the Motley::CommandDoubleDash class.
 */
class T210_MotleyCommandDoubleDashTest extends Testcase {

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

<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;
use Motley\Command;

/// Tests the Motley::Command class.
class T221_MotleyCommandTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $cmd1 = new Command();
        $this->assertInstanceOf(Command::class,$cmd1);
        $cmd2 = new Command("cmd2");
        $this->assertInstanceOf(Command::class,$cmd2);
        $cmd3 = new Command("cmd3","A test instance.");
        $this->assertInstanceOf(Command::class,$cmd3);
    }

    /// Test setCmdName/getCmdName functions.
    public function testSetGetCmdName() {
        $cmdName1a = "cmd1a";
        $cmdName1b = "cmd1b";
        $cmd1 = new Command($cmdName1a);
        $this->assertEquals($cmdName1a,$cmd1->getCmdName());
        $cmd1->setCmdName($cmdName1b);
        $this->assertEquals($cmdName1b,$cmd1->getCmdName());
    }

    /// Test setCmdDescription/getCmdDescription functions.
    public function testSetGetCmdDescription() {
        $cmdDesc1a = "cmd description 1a.";
        $cmdDesc1b = "cmd description 1b.";
        $cmd1 = new Command("cmd1",$cmdDesc1a);
        $this->assertEquals($cmdDesc1a,$cmd1->getCmdDescription());
        $cmd1->setCmdDescription($cmdDesc1b);
        $this->assertEquals($cmdDesc1b,$cmd1->getCmdDescription());
    }

}
?>

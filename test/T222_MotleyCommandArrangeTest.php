<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;
use Motley\Command;

/// Tests the Motley::Command class.
class T221_MotleyCommandArrangeTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $cmdArr1 = new CommandArrange();
        $this->assertInstanceOf(CommandArrange::class,$cmdArr1);
        $cmdArr2 = new Command("cmdArr2");
        $this->assertInstanceOf(CommandArrange::class,$cmdArr2);
        $cmdArr3 = new Command("cmdArr3","A test instance.");
        $this->assertInstanceOf(CommandArrange::class,$cmd3);
    }

    /// Test setCmdArrangeName/getCmdArrangeName functions.
    public function testSetGetCmdName() {
        $cmdArrName1a = "cmdArr1a";
        $cmdArrName1b = "cmdArr1b";
        $cmdArr1 = new CommandArrange($cmdArrName1a);
        $this->assertEquals($cmdArrName1a,$cmdArr1->getArrangeName());
        $cmdArr1->setArrangeName($cmdArr1b);
        $this->assertEquals($cmdArrName1b,$cmdArr1->getArrangeName());
    }

    /// Test setCmdDescription/getCmdDescription functions.
    public function testSetGetCmdDescription() {
        $cmdArrDesc1a = "cmdArr description 1a.";
        $cmdArrDesc1b = "cmdArr description 1b.";
        $cmdArr1 = new Command("cmdArr1",$cmdArrDesc1a);
        $this->assertEquals($cmdArrDesc1a,$cmdArr1->getArrangeDescription());
        $cmdArr1->setCmdDescription($cmdArrDesc1b);
        $this->assertEquals($cmdArrDesc1b,$cmdArr1->getArrangeDescription());
    }

    /// Test set/get display name functions.
    public function testSetGetDisplayName() {
        $arrangeName = "Normal Name";
        $arrangeDisplayName = "Display Name";
        $arrange = new CommandArrange($arrangeName);
        $this->assertEquals($arrangeName,$arrange->getDisplayName());
        $arrange->setDisplayName($arrangeDisplayName);
        $this->assertEquals($arrangeDisplayName,$arrange->getDisplayName());
    }
}
?>

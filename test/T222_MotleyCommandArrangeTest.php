<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;
use Motley\CommandArrange;
use Motley\CommandArg;
use Motley\CommandOpt;
use Motley\CommandOptGrp;
use Motley\CommandDoubleDash;
use Motley\UnitTestSupport;

/// Tests the Motley::Command class.
class T221_MotleyCommandArrangeTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $cmdArr1 = new CommandArrange();
        $this->assertInstanceOf(CommandArrange::class,$cmdArr1);
        $cmdArr2 = new CommandArrange("cmdArr2");
        $this->assertInstanceOf(CommandArrange::class,$cmdArr2);
        $cmdArr3 = new CommandArrange("cmdArr3","A test instance.");
        $this->assertInstanceOf(CommandArrange::class,$cmdArr3);
    }

    /// Test setCmdArrangeName/getCmdArrangeName functions.
    public function testSetGetCmdName() {
        $cmdArrName1a = "cmdArr1a";
        $cmdArrName1b = "cmdArr1b";
        $cmdArr1 = new CommandArrange($cmdArrName1a);
        $this->assertEquals($cmdArrName1a,$cmdArr1->getArrangeName());
        $cmdArr1->setArrangeName($cmdArrName1b);
        $this->assertEquals($cmdArrName1b,$cmdArr1->getArrangeName());
    }

    /// Test setCmdDescription/getCmdDescription functions.
    public function testSetGetCmdDescription() {
        $cmdArrDesc1a = "cmdArr description 1a.";
        $cmdArrDesc1b = "cmdArr description 1b.";
        $cmdArr1 = new CommandArrange("cmdArr1",$cmdArrDesc1a);
        $this->assertEquals($cmdArrDesc1a,$cmdArr1->getArrangeDescription());
        $cmdArr1->setArrangeDescription($cmdArrDesc1b);
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

    /// Test add/get/clear arrangement component array.
    public function testDefineGetClearArrangement() {
        $arran = new CommandArrange("arran1");
        $exparr = array();
        $this->assertEquals($exparr,$arran->getComponents());
        $opt1 = new CommandOpt("opt1","opt1 desc",array("-x"));
        $opt2 = new CommandOpt("opt2","opt2 desc",array("-y"));
        $optgrp = new CommandOptGrp("optgrp");
        $optgrp->addOption($opt2);
        $arg = new CommandArg("arg1");
        $components = array($opt1,$optgrp,$arg);
        $arran->addOpt($opt1,false,false);
        $arran->addOptGrp($optgrp,false);
        $arran->addArg($arg);
        $this->assertEquals(3,count($arran->getComponents()));
        $dd = new CommandDoubleDash();
        $arran->addDoubleDash($dd);
        $this->assertEquals(4,count($arran->getComponents()));
        $arran->clearComponents();
        $this->assertEquals(array(),$arran->getComponents());
    }

}
?>

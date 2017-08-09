<?php
/// Source code file for Motley::Test::CommandArrangeTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandArrange;
use Motley\CommandArg;
use Motley\CommandOpt;
use Motley\CommandOptGrp;
use Motley\CommandDoubleDash;
use Motley\UnitTestSupport;

/// Tests the Motley::CommandArrange class.
class CommandArrangeTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $cmdArr1 = new CommandArrange();
        $this->assertInstanceOf(CommandArrange::class,$cmdArr1);
        $cmdArr2 = new CommandArrange("cmdArr2");
        $this->assertInstanceOf(CommandArrange::class,$cmdArr2);
        $cmdArr3 = new CommandArrange("cmdArr3","A test instance.");
        $this->assertInstanceOf(CommandArrange::class,$cmdArr3);
    }

    /// Test setArrangeName/getArrangeName functions.
    public function testSetGetName() {
        $cmdArrName1a = "cmdArr1a";
        $cmdArrName1b = "cmdArr1b";
        $cmdArr1 = new CommandArrange($cmdArrName1a);
        $this->assertEquals($cmdArrName1a,$cmdArr1->getName());
        $cmdArr1->setName($cmdArrName1b);
        $this->assertEquals($cmdArrName1b,$cmdArr1->getName());
    }

    /// Test setDescription/getDescription functions.
    public function testSetGetDescription() {
        $cmdArrDesc1a = "cmdArr description 1a.";
        $cmdArrDesc1b = "cmdArr description 1b.";
        $cmdArr1 = new CommandArrange("cmdArr1",$cmdArrDesc1a);
        $this->assertEquals($cmdArrDesc1a,$cmdArr1->getDescription());
        $cmdArr1->setDescription($cmdArrDesc1b);
        $this->assertEquals($cmdArrDesc1b,$cmdArr1->getDescription());
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
        $this->assertEquals($exparr,$arran->getArranComps());
        $opt1 = new CommandOpt("opt1","opt1 desc",array("-x"));
        $opt2 = new CommandOpt("opt2","opt2 desc",array("-y"));
        $optgrp = new CommandOptGrp("optgrp");
        $optgrp->addOption($opt2);
        $arg = new CommandArg("arg1");
        $components = array($opt1,$optgrp,$arg);
        $arran->addComponent($opt1,false,false);
        $arran->addComponent($optgrp,false);
        $arran->addComponent($arg);
        $this->assertEquals(3,count($arran->getArranComps()));
        $dd = new CommandDoubleDash();
        $arran->addComponent($dd);
        $this->assertEquals(4,count($arran->getArranComps()));
        $arran->clearArranComps();
        $this->assertEquals(array(),$arran->getArranComps());
    }

}
?>

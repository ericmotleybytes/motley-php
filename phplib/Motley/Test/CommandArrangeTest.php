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
        # cmd -x -y <arg1>
        $this->assertEquals(3,count($arran->getArranComps()));
        $dd = new CommandDoubleDash();
        $arran->addComponent($dd);
        # cmd -x -y <arg1> --
        $this->assertEquals(4,count($arran->getArranComps()));
        $arran->clearArranComps();
        $this->assertEquals(array(),$arran->getArranComps());
    }

    public function testParse() {
        # define arrangement: cmd -h|--help
        $arranHelp = new CommandArrange("arranHelp");
        $optHelp   = new CommandOpt("optHelp","optHelp desc",array("-h","--help"));
        $arranHelp->addComponent($optHelp,false,false);
        $cmdv = array("dummy","-h");
        $matched = $arranHelp->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","--help");
        $matched = $arranHelp->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy");
        $matched = $arranHelp->parse($cmdv);
        $this->assertFalse($matched);
        $cmdv = array("dummy","-x");
        $matched = $arranHelp->parse($cmdv);
        $this->assertFalse($matched);
        # define arrangement: cmd -e|--echo [--] arg...
        $arranEcho = new CommandArrange("arranEcho");
        $optEcho   = new CommandOpt("optEcho","",array("-e","--echo"));
        $ddEcho    = new CommandDoubleDash("ddEcho");
        $argEcho   = new CommandArg("argEcho");
        $arranEcho->addComponent($optEcho,false,false);
        $arranEcho->addComponent($ddEcho,true,false);
        $arranEcho->addComponent($argEcho,false,true);
        # test parsing the arrangement
        $cmdv = array("dummy","-e","arg1");
        $matched = $arranEcho->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","--echo","arg1");
        $matched = $arranEcho->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-e","arg1","arg2");
        $matched = $arranEcho->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-e","--","arg1");
        $matched = $arranEcho->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-e","--","arg1","arg2");
        $matched = $arranEcho->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-e","--","arg1","-x");
        $matched = $arranEcho->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-e","arg1","--","arg2");
        $matched = $arranEcho->parse($cmdv);
        $this->assertFalse($matched);
        # define arrangement: cmd [-a|--aa] [-b|--bb]
        $arranAb = new CommandArrange("arranAb");
        $optA    = new CommandOpt("optA","",array("-a","--aa"));
        $optB    = new CommandOpt("optB","",array("-b","--bb"));
        $arranAb->addComponent($optA,true,false);
        $arranAb->addComponent($optB,false,false);
        $cmdv = array("dummy","-a","-b");
        $matched = $arranAb->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-b");
        $matched = $arranAb->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-a");
        $matched = $arranAb->parse($cmdv);
        $this->assertFalse($matched);
        # define arrangement: cmd [-a|-b]
        $arranAB  = new CommandArrange("arranAB");
        $optA     = new CommandOpt("optA","",array("-a","--aa"));
        $optB     = new CommandOpt("optB","",array("-b","--bb"));
        $optGrpAB = new CommandOptGrp("optGrpAB");
        $optGrpAB->addOption($optA);
        $optGrpAB->addOption($optB);
        $arranAB->addComponent($optGrpAB,true,true);
        $cmdv = array("dummy","-a");
        $matched = $arranAB->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-b");
        $matched = $arranAB->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","-a","-b");
        $matched = $arranAB->parse($cmdv);
        $this->assertTrue($matched);
        # define arrangement: cmd dump <file>...
        $arranDump = new CommandArrange("arranColor");
        $argDump   = new CommandArg("dump");
        $argDump->addValidLiteral("dump");
        $argFile   = new CommandArg("file");
        $argFile->addValidRegEx('/^.+\.txt$/');
        $arranDump->addComponent($argDump,false,false);
        $arranDump->addComponent($argFile,false,true);
        $cmdv = array("dummy","dump","thing.txt");
        $matched = $arranDump->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","read","thing.txt");
        $matched = $arranDump->parse($cmdv);
        $this->assertFalse($matched);
        $cmdv = array("dummy","dump");
        $matched = $arranDump->parse($cmdv);
        $this->assertFalse($matched);
        $cmdv = array("dummy","dump","thing.dat");
        $matched = $arranDump->parse($cmdv);
        $this->assertFalse($matched);
        # define arrangement: cmd dump [<file>...]
        $arranDump = new CommandArrange("arranColor");
        $argDump   = new CommandArg("dump");
        $argDump->addValidLiteral("dump");
        $argFile   = new CommandArg("file");
        $argFile->addValidRegEx('/^.+\.txt$/');
        $arranDump->addComponent($argDump,false,false);
        $arranDump->addComponent($argFile,true,true);
        $cmdv = array("dummy","dump","thing.txt");
        $matched = $arranDump->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","read","thing.txt");
        $matched = $arranDump->parse($cmdv);
        $this->assertFalse($matched);
        $reason = $arranDump->getMatchingReason();
        $this->assertStringStartsWith("MISMATCH",$reason);
        $cmdv = array("dummy","dump");
        $matched = $arranDump->parse($cmdv);
        $this->assertTrue($matched);
        $cmdv = array("dummy","dump","thing.dat");
        $matched = $arranDump->parse($cmdv);
        $this->assertFalse($matched);
        # define arrangement: cmd --cats [<catname>...] --dogs [<dogname>...]
        $arranCD = new CommandArrange("arranCD");
        $optCats = new CommandOpt("optCats","",array("--cats"));
        $optDogs = new CommandOpt("optCats","",array("--dogs"));
        $argCats = new CommandArg("argCats");
        $argDogs = new CommandArg("argDogs");
        $arranCD->addComponent($optCats,true,false);
        $arranCD->addComponent($argCats,true,true);
        $arranCD->addComponent($optDogs,true,false);
        $arranCD->addComponent($argDogs,true,true);
        $cmdv = array("dummy","--cats","fluffy","smokie","--dogs","fido","ralph");
        $matched = $arranCD->parse($cmdv);
        $this->assertTrue($matched);
        $comps = $arranCD->getMatchingComponents();
        $this->assertEquals(4,count($comps));
        $this->assertEquals($optCats,$comps[0]);
        $this->assertEquals($argCats,$comps[1]);
        $this->assertEquals($optDogs,$comps[2]);
        $this->assertEquals($argDogs,$comps[3]);
        $reason = $arranCD->getMatchingReason();
        $this->assertStringStartsWith("MATCH",$reason);
    }

}
?>

<?php
/// Source code file for Motley::Test::CommandOptGrpTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandOpt;
use Motley\CommandOptGrp;
use Motley\CommandArg;
use Motley\UnitTestSupport;

/// Tests the Motley::CommandOpt class.
class CommandOptGrpTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $optGrp1 = new CommandOptGrp();
        $this->assertInstanceOf(CommandOptGrp::class,$optGrp1);
        $optGrp2 = new CommandOptGrp("optGrp2");
        $this->assertInstanceOf(CommandOptGrp::class,$optGrp2);
        $optGrp3 = new CommandOptGrp("opt3Grp","A test instance.");
        $this->assertInstanceOf(CommandOptGrp::class,$optGrp3);
    }

    /// Test add/get/clear options.
    public function testAddGetClearOptSwitches() {
        $opt1 = new CommandOpt("opt1","",array("-a"));
        $opt2 = new CommandOpt("opt2","",array("-b"));
        $opt3 = new CommandOpt("opt3","",array("-b"));  // dup switch
        $optGrp = new CommandOptGrp("optGrp");
        $expOptions = array();
        $this->assertEquals($expOptions,$optGrp->getOptions());
        $expOptions[] = $opt1;
        $optCnt = $optGrp->addOption($opt1);
        $this->assertEquals(1,$optCnt);
        $this->assertEquals($expOptions,$optGrp->getOptions());
        $expOptions[] = $opt2;
        $optCnt = $optGrp->addOption($opt2);
        $this->assertEquals(2,$optCnt);
        $this->assertEquals($expOptions,$optGrp->getOptions());
        # try adding a dup option
        UnitTestSupport::engageCaptureHandler(E_USER_WARNING);
        $optCnt = $optGrp->addOption($opt2);
        UnitTestSupport::disengageCaptureHandler();
        $errs = UnitTestSupport::getCapturedErrors();
        $this->assertEquals(1,count($errs));
        $this->assertEquals(2,$optCnt);
        $this->assertEquals($expOptions,$optGrp->getOptions());
        # try adding a duplicate switch
        UnitTestSupport::engageCaptureHandler(E_USER_WARNING);
        $optCnt = $optGrp->addOption($opt3);
        UnitTestSupport::disengageCaptureHandler();
        $errs = UnitTestSupport::getCapturedErrors();
        $this->assertEquals(1,count($errs));
        $this->assertEquals(2,$optCnt);
        $this->assertEquals($expOptions,$optGrp->getOptions());
        # clear the options
        $expOptions = array();
        $optGrp->clearOptions();
        $this->assertEquals($expOptions,$optGrp->getOptions());
    }

    /// Test get switches for entire option group.
    public function testGetSwitches() {
        $optGrp = new CommandOptGrp("optGrp");
        $sa1 = array("-a","--aa");
        $sa2 = array("-b","--bb");
        $exp  = array_merge($sa1,$sa2);
        sort($exp);
        $opt1 = new CommandOpt("opt1","",$sa1);
        $opt2 = new CommandOpt("opt2","",$sa2);
        $optGrp->addOption($opt1);
        $optGrp->addOption($opt2);
        $act = $optGrp->getSwitches();
        sort($act);
        $this->assertEquals($exp,$act);
        # try finding option by swich
        $opt3 = $optGrp->getOptionBySwitch("--zzz");
        $this->assertNull($opt3);   // nothing found
        $opt3 = $optGrp->getOptionBySwitch("-b");
        $this->assertEquals($opt2,$opt3);   // found 2nd option
        $opt3 = $optGrp->getOptionBySwitch("--aa");
        $this->assertEquals($opt1,$opt3);   // found 1st option
    }

    /// Test validate functions.
    public function testValidate() {
        $optGrp = new CommandOptGrp("optGrp");
        $sa1 = array("-a","--aa");
        $sa2 = array("-b","--bb");
        $opt1 = new CommandOpt("opt1","",$sa1);
        $opt2 = new CommandOpt("opt2","",$sa2);
        $arg  = new CommandArg("arg");
        $opt2->setOptArg($arg,false);
        $optGrp->addOption($opt1);
        $optGrp->addOption($opt2);
        $this->assertTrue($optGrp->validate("-a"));
        $this->assertEquals($opt1,$optGrp->getLastValidOption());
        $this->assertFalse($optGrp->validate("-z"));
        $this->assertFalse($optGrp->validate("--zz"));
        $this->assertTrue($optGrp->validate("--aa"));
        $this->assertEquals($opt1,$optGrp->getLastValidOption());
        $this->assertFalse($optGrp->validate("-a thing"));
        $this->assertFalse($optGrp->validate("--aa=thing"));
        $this->assertTrue($optGrp->validate("-b thing"));
        $this->assertEquals($opt2,$optGrp->getLastValidOption());
        $this->assertTrue($optGrp->validate("--bb=thing"));
        $this->assertEquals($opt2,$optGrp->getLastValidOption());
        $this->assertFalse($optGrp->validate("-b"));
        $this->assertFalse($optGrp->validate("--bb"));
        $this->assertFalse($optGrp->validate("--bb thing"));
    }
}
?>

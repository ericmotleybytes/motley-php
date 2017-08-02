<?php
/// Source code file for Motley::Test::CommandOptTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandArg;
use Motley\CommandOpt;

/// Tests the Motley::CommandOpt class.
class CommandOptTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $opt1 = new CommandOpt();
        $this->assertInstanceOf(CommandOpt::class,$opt1);
        $opt2 = new CommandOpt("opt2");
        $this->assertInstanceOf(CommandOpt::class,$opt2);
        $opt3 = new CommandOpt("opt3","A test instance.");
        $this->assertInstanceOf(CommandOpt::class,$opt3);
    }

    /// Test cloning.
    public function testClone() {
        $opt1 = new CommandOpt("opt1","this is opt1");
        $this->assertInstanceOf(CommandOpt::class,$opt1);
        $opt2 = clone $opt1;
        $this->assertInstanceOf(CommandOpt::class,$opt2);
        $this->assertEquals($opt1->getOptName(),$opt2->getOptName());
        $this->assertEquals($opt1->getOptDescription(),
            $opt2->getOptDescription());
        $this->assertNotEquals($opt1->getInstanceGuid(),
            $opt2->getInstanceGuid());
    }

    /// Test copy functions.
    public function testCopy() {
        $opt1 = new CommandOpt("opt1","this is opt1");
        $this->assertInstanceOf(CommandOpt::class,$opt1);
        $opt2 = $opt1->copy();
        $this->assertInstanceOf(CommandOpt::class,$opt2);
        $this->assertEquals($opt1->getOptName(),$opt2->getOptName());
        $this->assertEquals($opt1->getOptDescription(),
            $opt2->getOptDescription());
        $this->assertNotEquals($opt1->getInstanceGuid(),
            $opt2->getInstanceGuid());
        $opt3name = "opt3";
        $opt3desc = "this is opt3";
        $opt3 = $opt1->copy($opt3name,$opt3desc);
        $this->assertEquals($opt3name,$opt3->getOptName());
        $this->assertEquals($opt3desc,$opt3->getOptDescription());
    }

    /// Test setOptName/getOptName functions.
    public function testSetGetOptName() {
        $optName1a = "opt1a";
        $optName1b = "opt1b";
        $opt1 = new CommandOpt($optName1a);
        $this->assertEquals($optName1a,$opt1->getOptName());
        $opt1->setOptName($optName1b);
        $this->assertEquals($optName1b,$opt1->getOptName());
    }

    /// Test setOptDescription/getOptDescription functions.
    public function testSetGetOptDescription() {
        $optDesc1a = "opt description 1a.";
        $optDesc1b = "opt description 1b.";
        $opt1 = new CommandOpt("opt1",$optDesc1a);
        $this->assertEquals($optDesc1a,$opt1->getOptDescription());
        $opt1->setOptDescription($optDesc1b);
        $this->assertEquals($optDesc1b,$opt1->getOptDescription());
    }

    /// Test getInstanceGuid function.
    public function testGetInstanceGuid() {
        $opt1 = new CommandOpt();
        $guid = $opt1->getInstanceGuid();
        $this->assertEquals(32,strlen($guid));
        $this->assertRegExp('/^[0-9a-f]{32}$/',$guid);
    }

    /// Test checkOptionSwitch.
    public function testCheckOptSwitch() {
        $opt1 = new CommandOpt("opt1","opt1 description.");
        $this->assertTrue($opt1->checkOptSwitch('-v'));
        $this->assertTrue($opt1->checkOptSwitch('--version'));
        $this->assertTrue($opt1->checkOptSwitch('--a_big-wide-switch'));
        $this->assertFalse($opt1->checkOptSwitch('-'));
        $this->assertFalse($opt1->checkOptSwitch('--'));
        $this->assertFalse($opt1->checkOptSwitch('---bogus'));
        $this->assertFalse($opt1->checkOptSwitch('-abc'));
        $this->assertFalse($opt1->checkOptSwitch(''));
    }

    /// Test add/get/clear option switches.
    public function testAddGetClearOptSwitches() {
        $opt1 = new CommandOpt("opt1","opt1 description.");
        $expSwitches = array();
        $this->assertEquals($expSwitches,$opt1->getOptSwitches());
        $expSwitches = array("-v","--version");
        $opt1->addOptSwitches($expSwitches);
        $this->assertEquals($expSwitches,$opt1->getOptSwitches());
        $expSwitches = array("-v","--version");
        $opt1->addOptSwitches($expSwitches);  // add dups to ignore
        $this->assertEquals($expSwitches,$opt1->getOptSwitches());
        $moreSwitches = array("--ver","--vers");
        $expSwitches = array_merge($expSwitches,$moreSwitches);
        $opt1->addOptSwitches($moreSwitches);
        $this->assertEquals($expSwitches,$opt1->getOptSwitches());
        $opt1->addOptSwitches(array());
        $this->assertEquals($expSwitches,$opt1->getOptSwitches());
        $opt1->clearOptSwitches();
        $this->assertEquals(array(),$opt1->getOptSwitches());
        # try adding a bad switch
        $saveErrorReporting = error_reporting(0); # turn off error reporting
        $cnt = $opt1->addOptSwitches(array("badswitch"));
        error_reporting($saveErrorReporting); # turn error reporting back on
        $this->assertEquals(0,count($opt1->getOptSwitches()));
    }

    /// Test set/get option argument functions.
    public function testSetGetOptArg() {
        $opt1 = new CommandOpt("opt1","opt1 description.");
        $this->assertNull($opt1->getOptArg());
        $arg1a = new CommandArg("arg1","arg1 description.");
        $arg1aGuid = $arg1a->getInstanceGuid();
        $opt1->setOptArg($arg1a);
        $arg1b = $opt1->getOptArg();
        $this->assertEquals($arg1aGuid,$arg1b->getInstanceGuid());
        $this->assertFalse($opt1->getOptArgOptional());
        $opt2 = new CommandOpt("opt2","opt2 description.");
        $arg2 = new CommandArg("arg2","arg2 description.");
        $opt2->setOptArg($arg2,true);
        $this->assertTrue($opt2->getOptArgOptional());
    }

    /// Test getSwitchesString.
    public function testGetSwitchesString() {
        $opt1 = new CommandOpt("opt1","Option a.");
        $opt1->addOptSwitches(array("-a","--aaa"));
        $exp="-a | --aaa";
        $act = $opt1->getSwitchesString();
        $this->assertEquals($exp,$act);
        $arg1 = new CommandArg("arg1","Argument a.");
        $opt1->setOptArg($arg1,false);
        $exp='-a | --aaa=<arg1>';
        $act = $opt1->getSwitchesString();
        $this->assertEquals($exp,$act);
        $opt1->setOptArg($arg1,true);
        $exp='-a | --aaa[=<arg1>]';
        $act = $opt1->getSwitchesString();
        $this->assertEquals($exp,$act);
        $opt1->addOptSwitches(array("-b","--bbb"));
        $exp='-a,-b | --aaa,--bbb[=<arg1>]';
        $act = $opt1->getSwitchesString();
        $this->assertEquals($exp,$act);
        $opt1->clearOptSwitches();
        $opt1->addOptSwitches(array("-a","-b"));
        $exp='-a,-b [<arg1>]';
        $act = $opt1->getSwitchesString();
        $this->assertEquals($exp,$act);
        $opt1->setOptArg($arg1,false);
        $exp='-a,-b <arg1>';
        $act = $opt1->getSwitchesString();
        $this->assertEquals($exp,$act);
    }
}
?>

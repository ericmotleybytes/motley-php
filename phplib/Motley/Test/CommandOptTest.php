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
        $opt1->setOptArg($arg1a);
        $arg1b = $opt1->getOptArg();
        $this->assertEquals($arg1a,$arg1b);
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

    // Test validate function.
    public function testValidate() {
        // do some tests with no associated argument.
        $opt = new CommandOpt("opt1","Option a.");
        $opt->addOptSwitches(array("-a","--aaa"));
        $valids = array("-a","--aaa");
        $invalids = array("-b","--bbb","bork","-","--","-a bork","--aaa=bork");
        foreach($valids as $valid) {
            $result = $opt->validate($valid);
            $this->assertStringStartsWith("VALID",$opt->getLastParamMessage());
            $this->assertTrue($result);
            $this->assertTrue($opt->getLastParamIsValid());
            $this->assertEquals($valid,$opt->getLastParamValue());
            $this->assertStringStartsWith("VALID",$opt->getLastParamMessage());
        }
        foreach($invalids as $invalid) {
            $result = $opt->validate($invalid);
            $this->assertStringStartsWith("INVALID",$opt->getLastParamMessage());
            $this->assertFalse($result);
            $this->assertFalse($opt->getLastParamIsValid());
            $this->assertEquals($invalid,$opt->getLastParamValue());
            $this->assertStringStartsWith("INVALID",$opt->getLastParamMessage());
        }
        // now add a non-optional argument
        $arg = new CommandArg("arg","Argument for unit testing.");
        $arg->addValidLiteral("red"  ,"Red is a warm color.");
        $arg->addValidLiteral("green","Green is a cool color.");
        $arg->addValidLiteral("blue" ,"Blue is a very cool color.");
        $arg->setDefaultValue("blue");
        $opt->setOptArg($arg,false);
        $valids = array("-a red","--aaa=green","-a -","--aaa=-");
        $invalids = array("-a","-a purple","--aaa","--aaa=yellow");
        foreach($valids as $valid) {
            $result = $opt->validate($valid);
            $this->assertStringStartsWith("VALID",$opt->getLastParamMessage());
            $this->assertTrue($result);
            $this->assertTrue($opt->getLastParamIsValid());
            $params = $opt->getLastParamValue();
            $this->assertTrue(is_array($params));
        }
        foreach($invalids as $invalid) {
            $result = $opt->validate($invalid);
            $this->assertStringStartsWith("INVALID",$opt->getLastParamMessage());
            $this->assertFalse($result);
            $this->assertFalse($opt->getLastParamIsValid());
            $params = $opt->getLastParamValue();
        }
        $valid = "-a red";
        $result = $opt->validate($valid);
        $params = $opt->getLastParamValue();
        $this->assertEquals("-a",$params[0]);
        $this->assertEquals("red",$params[1]);
        $valid = "--aaa=green";
        $result = $opt->validate($valid);
        $params = $opt->getLastParamValue();
        $this->assertEquals("--aaa",$params[0]);
        $this->assertEquals("green",$params[1]);
        // now make the argument optional
        $opt->setOptArg($arg,true);
        $arg->setDefaultValue("blue");
        $valids = array("-a red","--aaa=green","-a","--aaa","-a -","--aaa=-");
        $invalids = array("-a purple","--aaa=yellow");
        foreach($valids as $valid) {
            $result = $opt->validate($valid);
            $this->assertStringStartsWith("VALID",$opt->getLastParamMessage());
            $this->assertTrue($result);
            $this->assertTrue($opt->getLastParamIsValid());
            $params = $opt->getLastParamValue();
            $this->assertTrue(is_array($params));
        }
        foreach($invalids as $invalid) {
            $result = $opt->validate($invalid);
            $this->assertStringStartsWith("INVALID",$opt->getLastParamMessage());
            $this->assertFalse($result);
            $this->assertFalse($opt->getLastParamIsValid());
            $params = $opt->getLastParamValue();
        }
        $valid = "-a red";
        $result = $opt->validate($valid);
        $params = $opt->getLastParamValue();
        $this->assertEquals("-a",$params[0]);
        $this->assertEquals("red",$params[1]);
        $valid = "--aaa=green";
        $result = $opt->validate($valid);
        $params = $opt->getLastParamValue();
        $this->assertEquals("--aaa",$params[0]);
        $this->assertEquals("green",$params[1]);
        $valid = "-a";
        $result = $opt->validate($valid);
        $params = $opt->getLastParamValue();
        $this->assertEquals("-a",$params[0]);
        $this->assertEquals("blue",$params[1]);
        $valid = "--aaa";
        $result = $opt->validate($valid);
        $params = $opt->getLastParamValue();
        $this->assertEquals("--aaa",$params[0]);
        $this->assertEquals("blue",$params[1]);
        $valid = "-a -";
        $result = $opt->validate($valid);
        $params = $opt->getLastParamValue();
        $this->assertEquals("-a",$params[0]);
        $this->assertEquals("blue",$params[1]);
        $valid = "--aaa=-";
        $result = $opt->validate($valid);
        $params = $opt->getLastParamValue();
        $this->assertEquals("--aaa",$params[0]);
        $this->assertEquals("blue",$params[1]);
    }
}
?>

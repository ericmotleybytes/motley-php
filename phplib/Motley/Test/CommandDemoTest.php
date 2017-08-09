<?php
/// Source code file for Motley::Test::CommandDemoTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandDemo;

/// Tests the Motley::CommandOpt class.
class CommandDemoTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $demo = new CommandDemo("demoCmd","unit test instance");
        $this->assertInstanceOf(CommandDemo::class,$demo);
    }

    /// Test run function with default help.
    public function testRunDefaultHelp() {
        $demo = new CommandDemo("demoCmd","unit test instance");
        $argv = $demo->getArgv();
        $this->assertGreaterThan(0,count($argv));
        $argv = array();
        $argv[] = $demo->getDisplayName();
        $demo->setArgv($argv);
        $argv2 = $demo->getArgv();
        $this->assertEquals($argv,$argv2);
        $this->expectOutputRegex('/.*/');  # match anything
        $statcode = $demo->run(false);
        $outstr = $this->getActualOutput();
        $this->assertGreaterThan(0,strlen($outstr));
        $this->assertGreaterThan(0,substr_count($outstr,"--help"));
        $this->assertGreaterThan(0,substr_count($outstr,"--version"));
        $this->assertEquals(0,$statcode);
    }

    /// Test run function with help.
    public function testRunHelp() {
        $demo = new CommandDemo("demoCmd","unit test instance");
        # run with --help
        $argv = array();
        $argv[] = $demo->getDisplayName();
        $argv[] = "--help";
        $demo->setArgv($argv);
        $this->expectOutputRegex('/.*/');  # match anything
        $statcode = $demo->run(false);
        $outstr = $this->getActualOutput();
        $this->assertGreaterThan(0,strlen($outstr));
        $this->assertGreaterThan(0,substr_count($outstr,"--help"));
        $this->assertGreaterThan(0,substr_count($outstr,"--version"));
        $this->assertEquals(0,$statcode);
        # run with -h
        ob_clean();
        $argv = array();
        $argv[] = $demo->getDisplayName();
        $argv[] = "-h";
        $demo->setArgv($argv);
        $this->expectOutputRegex('/.*/');  # match anything
        $statcode = $demo->run(false);
        $outstr = $this->getActualOutput();
        $this->assertGreaterThan(0,strlen($outstr));
        $this->assertGreaterThan(0,substr_count($outstr,"--help"));
        $this->assertGreaterThan(0,substr_count($outstr,"--version"));
        $this->assertEquals(0,$statcode);
    }

    /// Test run function with version.
    public function testRunVersion() {
        $demo = new CommandDemo("demoCmd","unit test instance");
        # run with --version
        $argv = array();
        $argv[] = $demo->getDisplayName();
        $argv[] = "--version";
        $demo->setArgv($argv);
        $this->expectOutputRegex('/.*/');  # match anything
        $statcode = $demo->run(false);
        $outstr = $this->getActualOutput();
        $this->assertGreaterThan(0,strlen($outstr));
        $this->assertGreaterThan(0,substr_count($outstr,"version"));
        $this->assertEquals(0,$statcode);
        # run with -v
        ob_clean();
        $argv = array();
        $argv[] = $demo->getDisplayName();
        $argv[] = "-v";
        $demo->setArgv($argv);
        $this->expectOutputRegex('/.*/');  # match anything
        $statcode = $demo->run(false);
        $outstr = $this->getActualOutput();
        $this->assertGreaterThan(0,strlen($outstr));
        $this->assertGreaterThan(0,substr_count($outstr,"version"));
        $this->assertEquals(0,$statcode);
    }

    /// Test run function with bad switch.
    public function testRunBadSwitch() {
        $demo = new CommandDemo("demoCmd","unit test instance");
        $msg  = $demo->getMessenger();
        # run with --badswitch
        $argv = array();
        $argv[] = $demo->getDisplayName();
        $argv[] = "--badswitch";
        $demo->setArgv($argv);
        $msg->setDisplayLevel($msg::ERROR_MSG,$msg::ERROR_NONE_LVL);
        $statcode = $demo->run(false);
        $msg->setDisplayLevel($msg::ERROR_MSG,$msg::ERROR_ALL_LVL);
        $this->assertNotEquals(0,$statcode);
        # run with -b
        $argv = array();
        $argv[] = $demo->getDisplayName();
        $argv[] = "-b";
        $demo->setArgv($argv);
        $msg->setDisplayLevel($msg::ERROR_MSG,$msg::ERROR_NONE_LVL);
        $statcode = $demo->run(false);
        $msg->setDisplayLevel($msg::ERROR_MSG,$msg::ERROR_ALL_LVL);
        $this->assertNotEquals(0,$statcode);
    }
}
?>

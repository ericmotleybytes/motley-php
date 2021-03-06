<?php
/// Source code file for Motley::Test::CommandTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\Command;
use Motley\CommandArrange;
use Motley\CommandOpt;
use Motley\CommandMessenger;
use Motley\CommandIO;
use Motley\UsageFormatter;

/// Tests the Motley::Command class.
class CommandTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $cmd1 = new Command();
        $this->assertInstanceOf(Command::class,$cmd1);
        $cmd2 = new Command("cmd2");
        $this->assertInstanceOf(Command::class,$cmd2);
        $cmd3 = new Command("cmd3","A test instance.");
        $this->assertInstanceOf(Command::class,$cmd3);
    }

    /// Test setName/getName functions.
    public function testSetGetName() {
        $cmdName1a = "cmd1a";
        $cmdName1b = "cmd1b";
        $cmd1 = new Command($cmdName1a);
        $this->assertEquals($cmdName1a,$cmd1->getName());
        $cmd1->setName($cmdName1b);
        $this->assertEquals($cmdName1b,$cmd1->getName());
    }

    /// Test setDescription/getDescription functions.
    public function testSetGetDescription() {
        $cmdDesc1a = "cmd description 1a.";
        $cmdDesc1b = "cmd description 1b.";
        $cmd1 = new Command("cmd1",$cmdDesc1a);
        $this->assertEquals($cmdDesc1a,$cmd1->getDescription());
        $cmd1->setDescription($cmdDesc1b);
        $this->assertEquals($cmdDesc1b,$cmd1->getDescription());
    }

    /// Test set/get display name functions.
    public function testSetGetDisplayName() {
        $cmdName = "NormalName";
        $cmdDisplayName = "DisplayName";
        $cmd = new Command($cmdName);
        $this->assertEquals($cmdName,$cmd->getDisplayName());
        $cmd->setDisplayName($cmdDisplayName);
        $this->assertEquals($cmdDisplayName,$cmd->getDisplayName());
    }

    /// Test arrangement functions.
    public function testAddGetClearArrangements() {
        $cmd = new Command("cmd");
        $arrang1 = new CommandArrange("a1");
        $arrang2 = new CommandArrange("a2");
        $exparr = array();
        $this->assertEquals($exparr,$cmd->getArrangements());
        $exparr[] = $arrang1;
        $cmd->addArrangement($arrang1);
        $this->assertEquals($exparr,$cmd->getArrangements());
        $exparr[] = $arrang2;
        $cmd->addArrangement($arrang2);
        $this->assertEquals($exparr,$cmd->getArrangements());
        $exparr = array();
        $cmd->clearArrangements();
        $this->assertEquals($exparr,$cmd->getArrangements());
    }

    /// Test the get messenger and get formatter functions.
    public function testGetMessenger() {
        $cmd = new Command("cmd","A command for unit testing.");
        $msg = $cmd->getMessenger();
        $this->assertInstanceOf(CommandMessenger::class,$msg);
        $fmt = $cmd->getUsageFormatter();
        $this->assertInstanceOf(UsageFormatter::class,$fmt);
    }

    /// Test channel functions.
    public function testChannels() {
        $cmd = new Command("cmd","A command for unit testing.");
        $channels = $cmd->getChannelIds();
        $this->assertEquals(3,count($channels));
        $this->assertTrue(in_array(Command::InputIO,$channels));
        $this->assertTrue(in_array(Command::OutputIO,$channels));
        $this->assertTrue(in_array(Command::ErrorIO,$channels));
        $inputObj = $cmd->getChannel(Command::InputIO);
        $this->assertInstanceOf(CommandIO::class,$inputObj);
        $this->assertFalse($cmd->getChannel("bad_id"));
        $memId = "MemoryIO";
        $cmd->setChannel($memId,"php://memory","w+");
        $memObj = $cmd->getChannel($memId);
        $this->assertInstanceOf(CommandIO::class,$memObj);
        $cmd->setChannel($memId,"php://temp","w+");
        $memObj = $cmd->getChannel($memId);
        $this->assertInstanceOf(CommandIO::class,$memObj);
        $memObj = $cmd->getChannel($memId);  // get again on purpose for better coverage
        $this->assertInstanceOf(CommandIO::class,$memObj);
        $this->assertTrue($cmd->closeChannel($memId));
        $this->assertFalse($cmd->closeChannel($memId));
    }

    /// Test help functions.
    public function testHelp() {
        # only a partial test. CommandDemo does more testing.
        $cmd = new Command("cmd","A command for unit testing.");
        $fmt = $cmd->getUsageFormatter();
        $fmt->setColumnWidth(80);  # force width for testing
        $expHelp  = 'Name:' . PHP_EOL;
        $expHelp .= '  cmd - A command for unit testing.' . PHP_EOL;
        $expHelp .= 'Usage:' . PHP_EOL;
        $actHelp = $cmd->getHelp();
        $this->assertEquals(strlen($expHelp),strlen($actHelp));
        $this->assertEquals($expHelp,$actHelp);
        ob_start();
        ob_clean();
        $cmd->displayHelp();
        $actHelp = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(strlen($expHelp),strlen($actHelp));
        $this->assertEquals($expHelp,$actHelp);
    }

    /// Test argv functions.
    public function testArgv() {
        $cmd = new Command("cmd","A command for unit testing.");
        $cmd->setArgv();
        $a = $cmd->getArgv();
        $this->assertGreaterThan(0,count($a));
        $exp = array("dummy","one","two");
        $cmd->setArgv($exp);
        $act = $cmd->getArgv();
        $this->assertEquals($exp,$act);
    }

    /// Test parse.
    public function testParse() {
        # only a partial test. CommandDemo does more testing.
        $cmd = new Command("cmd","A command for unit testing.");
        $empty = $cmd->parse(array());
        $this->assertTrue(is_array($empty));
        $this->assertEquals(0,count($empty));
    }

    /// Test run.
    public function testRun() {
        $cmd = new Command("cmd","A command for unit testing.");
        $cmd->setArgv(array($cmd->getName()));
        $opt = new CommandOpt("testopt","",array("--test"));
        $arr = new CommandArrange("testarran","");
        $arr->addComponent($opt,false,false);
        $cmd->addArrangement($arr);
        $statcode = $cmd->run(false);
        $this->assertEquals(1,$statcode);
        $cmd->setArgv(array($cmd->getName(),"--test"));
        $statcode = $cmd->run(false);
        $this->assertEquals(0,$statcode);
    }
}
?>

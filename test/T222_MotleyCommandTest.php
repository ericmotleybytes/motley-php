<?php
/// Source code file for \T222_MotleyCommandTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.
/// @file

use PHPUnit\Framework\Testcase;
use Motley\Command;
use Motley\CommandArrange;

/// Tests the Motley::Command class.
class T222_MotleyCommandTest extends Testcase {

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

}
?>

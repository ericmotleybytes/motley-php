<?php
/// Source code file for Motley::Test::CommandArrangeCompTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandArg;
use Motley\CommandOpt;
use Motley\CommandOptGrp;
use Motley\CommandDoubleDash;
use Motley\CommandArrangeComp;

/// Tests the Motley::CommandArrangeComp class.
class CommandArrangeCompTest extends Testcase {

    /// Test instantiation and get functions.
    public function testNewGet() {
        $arg     = new CommandArg("arg");
        $opt     = new CommandArg("opt");
        $optGrp  = new CommandArg("optGrp");
        $dblDash = new CommandArg("dblDash");
        $aComp  = new CommandArrangeComp($arg);
        $this->assertInstanceOf(CommandArrangeComp::class,$aComp);
        $this->assertFalse($aComp->getIsOptional());
        $this->assertFalse($aComp->getIsRepeatable());
        $aComp = new CommandArrangeComp($opt,false);
        $this->assertInstanceOf(CommandArrangeComp::class,$aComp);
        $this->assertFalse($aComp->getIsOptional());
        $this->assertFalse($aComp->getIsRepeatable());
        $aComp = new CommandArrangeComp($optGrp,true,true);
        $this->assertInstanceOf(CommandArrangeComp::class,$aComp);
        $this->assertTrue($aComp->getIsOptional());
        $this->assertTrue($aComp->getIsRepeatable());
    }

    /// Test the getIsFulfilled function.
    public function testGetIsFulfilled() {
        $arg     = new CommandArg("arg");
        $aComp   = new CommandArrangeComp($arg,false,false);
        $this->assertFalse($aComp->getIsFulfilled()); # mandatory arg, no hist
        $arg->validate("bork");
        $this->assertTrue($aComp->getIsFulfilled()); # mandatory arg, has hist
        $arg     = new CommandArg("arg");
        $aComp   = new CommandArrangeComp($arg,true,false);
        $this->assertTrue($aComp->getIsFulfilled()); # optional arg, no hist
        $arg->validate("bork");
        $this->assertTrue($aComp->getIsFulfilled()); # optional arg, has hist
    }

}
?>

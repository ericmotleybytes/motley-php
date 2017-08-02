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

    /// Test setOptGrpName/getOptGrpName functions.
    public function testSetGetOptGrpName() {
        $optGrpName1a = "optGrp1a";
        $optGrpName1b = "opt1Grpb";
        $optGrp1 = new CommandOptGrp($optGrpName1a);
        $this->assertEquals($optGrpName1a,$optGrp1->getOptGrpName());
        $optGrp1->setOptGrpName($optGrpName1b);
        $this->assertEquals($optGrpName1b,$optGrp1->getOptGrpName());
    }

    /// Test setOptGrpDescription/getOptGrpDescription functions.
    public function testSetGetOptGrpDescription() {
        $optGrpDesc1a = "opt group description 1a.";
        $optGrpDesc1b = "opt group description 1b.";
        $optGrp1 = new CommandOptGrp("optGrp1",$optGrpDesc1a);
        $this->assertEquals($optGrpDesc1a,$optGrp1->getOptGrpDescription());
        $optGrp1->setOptGrpDescription($optGrpDesc1b);
        $this->assertEquals($optGrpDesc1b,$optGrp1->getOptGrpDescription());
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

    /// Test set/get display name functions.
    public function testSetGetDisplayName() {
        $optGrpName = "Normal Name";
        $optGrpDisplayName = "Display Name";
        $optGrp = new CommandOptGrp($optGrpName);
        $this->assertEquals($optGrpName,$optGrp->getDisplayName());
        $optGrp->setDisplayName($optGrpDisplayName);
        $this->assertEquals($optGrpDisplayName,$optGrp->getDisplayName());
    }

}
?>

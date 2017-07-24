<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;
use Motley\CommandOpt;
use Motley\CommandOptGrp;

/// Tests the Motley::CommandOpt class.
class T221_MotleyCommandOptGrpTest extends Testcase {

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
        $opt1 = new CommandOpt("opt1");
        $opt2 = new CommandOpt("opt2");
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
        $saveErrorReporting = error_reporting(0); # turn off error reporting
        $optCnt = $optGrp->addOption($opt2);
        error_reporting($saveErrorReporting); # turn error reporting back on
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

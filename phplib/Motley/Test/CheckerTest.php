<?php
/// Source code file for Motley::Test::CheckerTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\Checker;
use Motley\UnitTestSupport;

/// Tests the Motley::Checker class.
class CheckerTest extends Testcase {

    /// A unit test tear-down function to clean up possible test
    /// error handlers.
    protected function tearDown() {
        if (UnitTestSupport::$captureHandlerSet===true) {
            // @codeCoverageIgnoreStart
            restore_error_handler();
            UnitTestSupport::$captureHandlerSet = false;
            // @codeCoverageIgnoreEnd
        }
    }

    public function testGetSetErrorLevel() {
        $chk  = new Checker();
        $exp1 = E_USER_ERROR;
        $act1 = $chk->getErrorLevel();
        $this->assertEquals($exp1,$act1);
        $exp2 = E_USER_NOTICE;
        $chk->setErrorLevel($exp2);
        $this->assertEquals($exp2,$chk->getErrorLevel());
        $chk  = new Checker($exp2);
        $this->assertEquals($exp2,$chk->getErrorLevel());
    }

    public function testChecking() {
        // turn on error capturing
        UnitTestSupport::engageCaptureHandler(E_USER_NOTICE);
        // check the checker
        $chk  = new Checker(E_USER_NOTICE);
        $this->assertEquals(0,$chk->getFailCount());
        $chk->resetFailCount();
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $chk->resetPassCount();
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        // check checkTrue
        $stat = $chk->checkTrue(true);
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkTrue(1);  # not strictly exactly true
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkTrue(1==true);  # is strictly exactly true
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkTrue(1===true);  # is not strictly exactly true
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        // check checkFalse
        $stat = $chk->checkFalse(false);
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkFalse(null);
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkFalse(!(null==false));
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkFalse(null);
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        // check checkNotTrue
        $stat = $chk->checkNotTrue(true);
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkNotTrue(false);
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkNotTrue(1);
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkNotTrue(1==true);
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        // check checkNotFalse
        $stat = $chk->checkNotFalse(true);
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkNotFalse(false);
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkNotFalse(null);
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        // check checkIsZero
        $stat = $chk->checkIsZero(0);
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkIsZero(1);
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkIsZero(true);
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkIsZero("0");
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        // check checkIsNull
        $stat = $chk->checkIsNull(null);
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkIsNull("");
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        // check checkIsNotNull
        $stat = $chk->checkIsNotNull(null);
        $this->assertFalse($stat);
        $this->assertEquals(1,$chk->getFailCount());
        $this->assertEquals(0,$chk->getPassCount());
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        $stat = $chk->checkIsNotNull("");
        $this->assertTrue($stat);
        $this->assertEquals(0,$chk->getFailCount());
        $this->assertEquals(1,$chk->getPassCount());
        $this->assertEquals(0,count(UnitTestSupport::getCapturedErrors()));
        $chk->resetFailCount();
        $chk->resetPassCount();
        UnitTestSupport::clearCapturedErrors();
        // turn off error capturing
        UnitTestSupport::disengageCaptureHandler();
    }

    // Test the isAssociativeArray static function.
    public function testIsAssociativeArray() {
        $this->assertFalse(Checker::isAssociativeArray(""));
        $this->assertFalse(Checker::isAssociativeArray("x"));
        $this->assertFalse(Checker::isAssociativeArray(null));
        $this->assertFalse(Checker::isAssociativeArray(true));
        $this->assertFalse(Checker::isAssociativeArray(false));
        $this->assertFalse(Checker::isAssociativeArray(0));
        $this->assertFalse(Checker::isAssociativeArray(1));
        $this->assertFalse(Checker::isAssociativeArray(42));
        $this->assertFalse(Checker::isAssociativeArray(3.14157));
        $this->assertFalse(Checker::isAssociativeArray(array()));
        $this->assertFalse(Checker::isAssociativeArray(array("a","b")));
        $this->assertTrue(Checker::isAssociativeArray(array("a"=>"A","b"=>"B")));
    }

    // Test the describeVar static function.
    public function testDescribeVar() {
        $this->assertEquals("(boolean/true)'1'",Checker::describeVar(true));
        $this->assertEquals("(boolean/false)''",Checker::describeVar(false));
        $this->assertEquals("(NULL)''",Checker::describeVar(null));
        $this->assertEquals("(string)'abc'",Checker::describeVar("abc"));
        $this->assertEquals("(integer)'42'",Checker::describeVar(42));
        $this->assertEquals("[]",Checker::describeVar(array()));
        $this->assertEquals("[(integer)'1',(integer)'2']",
            Checker::describeVar(array(1,2)));
        $this->assertEquals("{(string)'a'=>(integer)'1',(string)'b'=>(integer)'2'}",
            Checker::describeVar(array("a"=>1,"b"=>2)));
        # try a truncation
        $this->assertEquals("(string)'abc...",
            Checker::describeVar("abcdefghijklmnopqrstuvwxyz",15));
    }
}
?>

<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

require_once(__DIR__ . '/ZZZ_SampleClass.php');

use PHPUnit\Framework\Testcase;
use Motley\UnitTestSupport;

/// Tests the Motley::UnitTestSupport class.
class T205_MotleyUnitTestSupportTest extends Testcase {

    /// A unit test tear-down function to clean up possible test
    /// error handlers.
    protected function tearDown() {
        if (UnitTestSupport::$captureHandlerSet===true) {
            restore_error_handler();
            UnitTestSupport::$captureHandlerSet = false;
        }
    }

    /// Test invokeFunction.
    public function testInvokeFunction() {
        $sam = new ZZZ_SampleClass();
        $this->assertEquals(ZZZ_SampleClass::PUBFUN,
            UnitTestSupport::invokeFunction($sam,"publicFunction"));
        $this->assertEquals(ZZZ_SampleClass::PROFUN,
            UnitTestSupport::invokeFunction($sam,"protectedFunction"));
        $this->assertEquals(ZZZ_SampleClass::PRIFUN,
            UnitTestSupport::invokeFunction($sam,"privateFunction"));
        $this->assertEquals(ZZZ_SampleClass::STAPUBFUN,
            UnitTestSupport::invokeFunction($sam,"staticPublicFunction"));
        $this->assertEquals(ZZZ_SampleClass::STAPROFUN,
            UnitTestSupport::invokeFunction($sam,"staticProtectedFunction"));
        $this->assertEquals(ZZZ_SampleClass::STAPRIFUN,
            UnitTestSupport::invokeFunction($sam,"staticPrivateFunction"));
    }

    /// Test the capture error handler.
    public function testCaptureHandler() {
        $noticeMsg  = "notice";
        $warningMsg = "warning";
        $errorMsg   = "error";
        UnitTestSupport::engageCaptureHandler(E_USER_NOTICE|E_USER_WARNING|E_USER_ERROR);
        trigger_error($noticeMsg,E_USER_NOTICE);
        $errs = UnitTestSupport::getCapturedErrors();
        $this->assertEquals(1,count($errs));
        $err = $errs[0];
        $this->assertEquals($noticeMsg,$err[UnitTestSupport::ERRSTR]);
        UnitTestSupport::clearCapturedErrors();
        trigger_error($warningMsg,E_USER_WARNING);
        $errs = UnitTestSupport::getCapturedErrors();
        $this->assertEquals(1,count($errs));
        $err = $errs[0];
        $this->assertEquals($warningMsg,$err[UnitTestSupport::ERRSTR]);
        UnitTestSupport::clearCapturedErrors();
        trigger_error($errorMsg,E_USER_ERROR);
        $errs = UnitTestSupport::getCapturedErrors();
        $this->assertEquals(1,count($errs));
        $err = $errs[0];
        $this->assertEquals($errorMsg,$err[UnitTestSupport::ERRSTR]);
        UnitTestSupport::clearCapturedErrors();
        UnitTestSupport::disengageCaptureHandler();
    }
}
?>

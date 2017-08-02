<?php
/// Source code file for Motley::Test::UnitTestSupportTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

require_once(__DIR__ . '/ZzzSampleClass.php');

use PHPUnit\Framework\Testcase;
use Motley\UnitTestSupport;
use Motley\Test\ZzzSampleClass;

/// Tests the Motley::UnitTestSupport class.
class UnitTestSupportTest extends Testcase {

    protected $lastUnit = null;  ///< Last interesting UnitTestSupport instance.

    /// A unit test tear-down function to clean up possible test
    /// error handlers.
    protected function tearDown() {
        // @codeCoverageIgnoreStart
        if (UnitTestSupport::$captureHandlerSet===true) {
            restore_error_handler();
            UnitTestSupport::$captureHandlerSet = false;
        }
        if(!is_null($this->lastUnit)) {
            $this->lastUnit->cleanupTmp();
        }
        // @codeCoverageIgnoreEnd
    }

    /// Test invokeFunction.
    public function testInvokeFunction() {
        $sam = new ZzzSampleClass();
        $this->assertEquals(ZzzSampleClass::PUBFUN,
            UnitTestSupport::invokeFunction($sam,"publicFunction"));
        $this->assertEquals(ZzzSampleClass::PROFUN,
            UnitTestSupport::invokeFunction($sam,"protectedFunction"));
        $this->assertEquals(ZzzSampleClass::PRIFUN,
            UnitTestSupport::invokeFunction($sam,"privateFunction"));
        $this->assertEquals(ZzzSampleClass::STAPUBFUN,
            UnitTestSupport::invokeFunction($sam,"staticPublicFunction"));
        $this->assertEquals(ZzzSampleClass::STAPROFUN,
            UnitTestSupport::invokeFunction($sam,"staticProtectedFunction"));
        $this->assertEquals(ZzzSampleClass::STAPRIFUN,
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

    /// Test the temporary dir/file routines.
    public function testRecursiveDirectoryDelete() {
        $unt = new UnitTestSupport();
        $this->assertInstanceOf(UnitTestSupport::class,$unt);
        $this->lastUnit = $unt;
        $exp1 = '../../test/tmp';
        $tmpdir1 = $unt->getTmpDirRoot();
        $this->assertStringEndsWith($exp1,$tmpdir1);
        $tmpdir2 = __DIR__ . '/tmp';
        $unt->setTmpDirRoot($tmpdir2);
        $this->assertEquals($tmpdir2,$unt->getTmpDirRoot());
        $tmproot = $unt->createTmpRootDir();
        $this->assertGreaterThan(0,strlen($tmproot));
        $dircnt1 = count($unt->getTmpDirsCreated());
        # make a specific tmp subdir
        $subdir1 = $unt->createTmpSubDir("bork");
        $this->assertStringEndsWith("bork",$subdir1);
        $expcnt2 = $dircnt1 + 1;
        $cDirs = $unt->getTmpDirsCreated();
        $this->assertEquals($expcnt2,count($cDirs));
        $tst = in_array($subdir1,$cDirs);
        # make a random tmp subdir
        $subdir2 = $unt->createTmpSubDir();
        $expcnt3 = $expcnt2 + 1;
        $cDirs = $unt->getTmpDirsCreated();
        $this->assertEquals($expcnt3,count($cDirs));
        $tst = in_array($subdir2,$cDirs);
        # create a temp dummy file
        $file1 = "$subdir2/borkbork.bork";
        $dummy = "bork\n";
        $file1a = $unt->createTmpDummyFile($file1,$dummy,100);
        $this->assertEquals($file1,$file1a);
        # create a rogue file
        $fh = fopen($file1a,"r");
        $line1 = fgets($fh);
        fclose($fh);
        $this->assertEquals($dummy,$line1);
        # create a rogue subdir
        $rogueDir = "$subdir2/rogue";
        mkdir($rogueDir);
        # test cleanup
        $saveFiles = $unt->getTmpFilesCreated();
        $saveDirs  = $unt->getTmpDirsCreated();
        $xtraFile = "$subdir2/xtra.tmp";
        $fh = fopen($xtraFile,"w");
        fclose($fh);
        $unt->cleanupTmp();
        foreach($saveFiles as $file) {
            $this->assertFileNotExists($file);
        }
        foreach($saveDirs as $dir) {
            $this->assertFileNotExists($dir);
        }
        $this->lastUnit = null;
    }
}
?>

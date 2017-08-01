<?php
/// Source code file for \T210_MotleyCommandMessengerTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.
/// @file

use PHPUnit\Framework\Testcase;
use Motley\CommandMessenger;
use Motley\GuidGenerator;
use Motley\UnitTestSupport;

/// Tests the Motley::CommandMessenger class.
class T210_MotleyCommandMessengerTest extends Testcase {
    protected $tmpdir = __DIR__ . '/tmp';  ///< Root of temporary directory.
    protected $lastUnit = null;  ///< Last interesting UnitTestSupport instance.

    /// A unit test set-up function.
    protected function setUp() {
        $this->lastUnit = new UnitTestSupport();
    }

    /// A unit test tear-down function.
    protected function tearDown() {
        if (UnitTestSupport::$captureHandlerSet===true) {
            restore_error_handler();
            UnitTestSupport::$captureHandlerSet = false;
        }
        if(!is_null($this->lastUnit)) {
            $this->lastUnit->cleanupTmp();
        }
    }

    /// Test instantiation.
    public function testNew() {
        $msg = new CommandMessenger();
        $this->assertInstanceOf(CommandMessenger::class,$msg);
    }

    // Test set/get error exit level.
    public function testSetGetErrorExitLevel() {
        $msg = new CommandMessenger();
        $exp1 = CommandMessenger::ERROR_ERROR_LVL;
        $this->assertEquals($exp1,$msg->getErrorExitLevel());
        $exp2 = CommandMessenger::ERROR_DIE_LVL;
        $msg->setErrorExitLevel($exp2);
        $this->assertEquals($exp2,$msg->getErrorExitLevel());
    }

    // test end-user message funtions.
    public function testEndUserMessaging() {
        $msg = new CommandMessenger();
        $unt = $this->lastUnit;
        $verboType = CommandMessenger::VERBO_MSG;
        $errorType = CommandMessenger::ERROR_MSG;
        $debugType = CommandMessenger::DEBUG_MSG;
        $msgTypes = array($verboType,$errorType,$debugType);
        $tmpdir = $this->lastUnit->createTmpRootDir();
        $subdir = $this->lastUnit->createTmpSubDir();
        $verboFile = "$subdir/verbo.out";
        $errorFile = "$subdir/error.out";
        $debugFile = "$subdir/debug.out";
        $msg->setOutputDestination($verboType,$verboFile);
        $verboDest = $msg->getOutputDestination($verboType);
        $this->assertEquals($verboFile,$verboDest);
        $msg->setOutputDestination($errorType,$errorFile);
        $errorDest = $msg->getOutputDestination($errorType);
        $this->assertEquals($errorFile,$errorDest);
        $msg->setOutputDestination($debugType,$debugFile);
        $debugDest = $msg->getOutputDestination($debugType);
        $this->assertEquals($debugFile,$debugDest);
        # disable actual dying.
        $msg->setErrorExitLevel(CommandMessenger::ERROR_DIE_LVL+1);  // do not die
        # try filtered messaging
        $msg->setDisplayLevel($verboType,CommandMessenger::VERBOSITY_NONE_LVL);
        $msg->setDisplayLevel($errorType,CommandMessenger::ERROR_NONE_LVL);
        $msg->setDisplayLevel($debugType,CommandMessenger::DEBUG_NONE_LVL);
        $this->assertEquals(0,$msg->getDisplayLevel($verboType));
        $this->assertEquals(0,$msg->getDisplayLevel($errorType));
        $this->assertEquals(0,$msg->getDisplayLevel($debugType));
        $msg->infoMessage("Info message 1.");
        $msg->verboseMessage("Verbose message 1.");
        $msg->warningMessage("Warning message 1.");
        $msg->errorMessage("Error message 1.");
        $msg->dieMessage("Die message 1.");
        $msg->debugMessage("Debug message 1.");
        $msg->verboseDebugMessage("Verbose debug message 1");
        foreach($msgTypes as $msgType) {
            $dest = $msg->getOutputDestination($msgType);
            $this->assertFileNotExists($dest);
        }
        # try unfiltered messaging
        $msg->setDisplayLevel($verboType,CommandMessenger::VERBOSITY_ALL_LVL);
        $msg->setDisplayLevel($errorType,CommandMessenger::ERROR_ALL_LVL);
        $msg->setDisplayLevel($debugType,CommandMessenger::DEBUG_ALL_LVL);
        $msg->infoMessage("Info message 1.");
        $msg->verboseMessage("Verbose message 1.");
        $msg->warningMessage("Warning message 1.");
        $msg->errorMessage("Error message 1.");
        $msg->dieMessage("Die message 1.");
        $msg->debugMessage("Debug message 1.");
        $msg->verboseDebugMessage("Verbose debug message 1");
        foreach($msgTypes as $msgType) {
            $dest = $msg->getOutputDestination($msgType);
            $this->assertFileExists($dest);
        }
        # try resetting verbo output destination
        $msg->setOutputDestination($verboType,CommandMessenger::DEFAULT_VERBO_DEST);
        $verboDest = $msg->getOutputDestination($verboType);
        $this->assertEquals(CommandMessenger::DEFAULT_VERBO_DEST,$verboDest);
    }

}
?>

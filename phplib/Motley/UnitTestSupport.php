<?php
/// Class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

use Motley\Checker;

/// Routines to support unit test routines.
class UnitTestSupport {

    const ERRNO   = "errno";   ///< Error hash key for error level.
    const ERRSTR  = "errstr";  ///< Error hash key for error message.
    const ERRFILE = "errfile"; ///< Error hash key for source code file.
    const ERRLINE = "errline"; ///< Error hash key for file line number.

    /// An array of hash arrays of captured errors.
    /// Each hash array has the following keys:
    /// "errno", "errstr", "errfile", and "errline".
    public static $capturedErrors = array();
    /// Keep track of whether the capture error handler is engaged.
    public static $captureHandlerSet = false;
    /// The saved old error handler, if it is replaced.
    public static $oldErrorHandler = null;

    /// Call a protected or private function in a class under test.
    /// This is a unit test helper function.
    /// @param $obj - A class object.
    /// @param $funcName - The name of the function to call.
    /// @param $argArr - Array of function arguments.
    /// @return Whatever the called function returns.
    public static function invokeFunction(&$obj, $funcName, array $argArr=array()) {
        $reflect  = new \ReflectionClass(get_class($obj));
        $function = $reflect->getMethod($funcName);
        $function->setAccessible(true);
        $result = $function->invokeArgs($obj,$argArr);
        return $result;
    }

    /// An error handler which simply captures the errors.
    /// @param $errno - The error level.
    /// @param $errstr - The error message.
    /// @param $errfile - The error file.
    /// @param $errline - The error line.
    public static function errorHandlerForCapturing(
        int $errno, string $errstr, string $errfile, int $errline) {
        $err = array();
        $err[self::ERRNO]   = $errno;
        $err[self::ERRSTR]  = $errstr;
        $err[self::ERRFILE] = $errfile;
        $err[self::ERRLINE] = $errline;
        self::$capturedErrors[] = $err;
        return true;
    }

    /// Set the capture error handler as an active error handler.
    /// @param $error_types - AN error level mask for errors calling this handler.
    public static function engageCaptureHandler(int $error_types=E_ALL|E_STRICT) {
        self::$capturedErrors = array();
        self::$oldErrorHandler = set_error_handler(
            "\Motley\UnitTestSupport::errorHandlerForCapturing", $error_types);
        self::$captureHandlerSet = true;
    }

    /// Disengage the capture handler and restore the original handler.
    public static function disengageCaptureHandler() {
        if(self::$captureHandlerSet===true) {
            restore_error_handler();
            self::$captureHandlerSet = false;
        }
    }

    /// Get all captured errors as an array of hash arrays.
    /// @return An array of hash arrays. Each hash array describes a captured
    /// error using "errno", "srrstr", "errfile" and "errline" keys.
    public static function getCapturedErrors() {
        return self::$capturedErrors;
    }

    /// Clear all captured errors.
    public static function clearCapturedErrors() {
        self::$capturedErrors = array();
    }

    #
    # Define non-static properties and functions.
    #
    protected $tmpRootDir      = null;         ///< Root temporary directory.
    protected $tmpDirsCreated  = array();      ///< Temp dirs created in session.
    protected $tmpFilesCreated = array();      ///< Temp files created in session.
    protected $chkE            = null;         ///< Checker instance for errors.

    /// CLass constructor.
    public function __construct() {
        $this->tmpRootDir = __DIR__ . '/../../test/tmp';
        $this->chkE = new Checker(E_USER_ERROR);
    }

    /// Set the base temporary directory.
    /// @param $rootDir - The absolute path to the root temporary directory.
    public function setTmpDirRoot(string $rootDir) {
        $this->tmpRootDir = $rootDir;
    }

    /// Get the base temporary directory.
    /// @return The absolute path to the root temporary directory.
    public function getTmpDirRoot() : string {
        return $this->tmpRootDir;
    }

    /// Create the root temporary directory if it does not yet exist.
    /// @return The absolute path to the root temporary directory.
    public function createTmpRootDir() : string {
        $chk = $this->chkE;
        if(!file_exists($this->tmpRootDir)) {
            $status = mkdir($this->tmpRootDir);
            if (! $chk->checkNotFalse($status,"mkdir failed.")) { return ""; }
            $this->tmpDirsCreated[] = $this->tmpRootDir;
        }
        return $this->tmpRootDir;
    }

    /// Create a temporary subdirectory under the root temporary directory.
    /// @param $relSubDir - The optional relative name of the subdirectory.
    /// If not specified, a randomly named subdirectory will be created.
    /// @return The absolute path of the created subdirectory.
    public function createTmpSubDir(string $relSubDir=null) {
        $chk = $this->chkE;
        if(is_null($relSubDir)) {
            $subDir = $this->tmpRootDir . "/" . rand();
        } else {
            $subDir = $this->tmpRootDir . "/" . $relSubDir;
        }
        if (!$chk->checkNotTrue(file_exists($subDir),"$subDir exists.")) { return ""; }
        $status = mkdir($subDir);
        if (!$chk->checkNotFalse($status,"mkdir $subDir failed.")) { return ""; }
        $this->tmpDirsCreated[] = $subDir;
        return $subDir;
    }

    /// Create a temporary dummy file.
    /// @param $filespec - absolute file path for temporary file.
    /// However, it must be in a temporary directory we created.
    /// @param $dummy - An optional string to be optionally repeated in the file.
    /// @param $repeat - The number of times to repeat $dummy.
    /// @return The absolute path of the file created.
    public function createTmpDummyFile(
        string $filespec, string $dummy="", int $repeat=0) {
        // verify $dir is a dir we created, or the root tempdir.
        $chk = $this->chkE;
        $dir = dirname($filespec);
        $tst = in_array($dir,$this->tmpDirsCreated);
        if(! $chk->checkTrue($tst,"$dir not created during session.")) { return ""; }
        $fh = fopen($filespec,"w");
        if(! $chk->checkNotFalse($fh,"fopen $filespec failed.")) { return ""; }
        $this->tmpFilesCreated[] = $filespec;
        $repeatCnt=0;
        while($repeatCnt<$repeat) {
            $bytes = fwrite($fh,$dummy);
            if(! $chk->checkNotFalse($bytes,"fwrite $filespec failed.")) { return ""; }
            $repeatCnt++;
        }
        fclose($fh);
        return $filespec;
    }

    /// Get the list of temp directories actually created.
    /// @return The list of temp directories actually created.
    public function getTmpDirsCreated() {
        return $this->tmpDirsCreated;
    }

    /// Get the list of temp files actually created.
    /// @return The list of temp files actually created.
    public function getTmpFilesCreated() {
        return $this->tmpFilesCreated;
    }

    /// Delete as much of tmp work as possible.
    function cleanupTmp() {
        // try to delete all the tmp files we know about.
        foreach($this->tmpFilesCreated as $file) {
            if(file_exists($file)) {
                $status = unlink($file);
            }
        }
        $this->tmpFilesCreated = array();
        // try to completely delete each directory we created.
        $dirs = array_reverse($this->tmpDirsCreated);
        foreach($dirs as $dir) {
            if(file_exists($dir)) {
                $this->recursiveRmdir($dir);
            }
        }
        $this->tmpDirsCreated = array();
    }

    /// Recursively delete a directory and all contents.
    /// @param $path - The top level directory to remove.
    public function recursiveRmdir(string $path) {
        if(!file_exists($path)) { return; }
        $files = glob($path . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->recursiveRmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($path);
    }

}
?>

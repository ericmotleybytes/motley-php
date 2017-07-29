<?php
/// Class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

/// Static routines to support unit test routines.
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
    public function engageCaptureHandler(int $error_types=E_ALL|E_STRICT) {
        self::$capturedErrors = array();
        self::$oldErrorHandler = set_error_handler(
            "\Motley\UnitTestSupport::errorHandlerForCapturing", $error_types);
        self::$captureHandlerSet = true;
    }

    /// Disengage the capture handler and restore the original handler.
    public function disengageCaptureHandler() {
        if(self::$captureHandlerSet===true) {
            restore_error_handler();
            self::$captureHandlerSet = false;
        }
    }

    /// Get all captured errors as an array of hash arrays.
    /// @return An array of hash arrays. Each hash array describes a captured
    /// error using "errno", "srrstr", "errfile" and "errline" keys.
    public function getCapturedErrors() {
        return self::$capturedErrors;
    }

    /// Clear all captured errors.
    public function clearCapturedErrors() {
        self::$capturedErrors = array();
    }

}
?>

<?php
/// Source code file for the Motley::Checker class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

/// Check smoothly for error conditions.
/// Checking via a separate package can also sometimes make it easier to
/// acheive 100% unit test code coverage without resorting to code ignore blocks.
/// @class Motley::Checker
class Checker {

    protected $errorLevel = E_USER_ERROR;   ///< Error level to use for this instance.
    protected $failCount = 0;               ///< Number of check fails since reset.
    protected $passCount = 0;               ///< Number of check passes since reset.

    /// Class constructor.
    /// @param $errLvl - The php error level this instance will trigger us a check fails.
    /// Multiple class instances can be instantiated at different error levels.
    public function __construct(int $errLvl=E_USER_ERROR) {
        $this->errorLevel = $errLvl;
    }

    /// Get the instance error level.
    /// @return The php error level used if a check fails.
    public function getErrorLevel() : int {
        return $this->errorLevel;
    }

    /// Set the instance error level.
    /// @param $errLvl - The php error level used if a check fails.
    public function setErrorLevel(int $errLvl=E_USER_ERROR) {
        $this->errorLevel=$errLvl;
    }

    /// Get the number of check fails since last reset.
    /// @return The number of check fails since last reset.
    public function getFailCount() {
        return $this->failCount;
    }

    /// Reset the number of check fails to zero.
    public function resetFailCount() {
        $this->failCount = 0;
    }

    /// Get the number of check passes since last reset.
    /// @return The number of check passes since last reset.
    public function getPassCount() {
        return $this->passCount;
    }

    /// Reset the number of check passes to zero.
    public function resetPassCount() {
        $this->passCount = 0;
    }

    /// Check that a condition is exactly strictly true.
    /// @param $cond - The condition to check.
    /// @param $failMsg - The message to display if the check fails.
    /// @return True if check ok, false if check failed.
    public function checkTrue($cond, string $failMsg="") : bool {
        if($failMsg=="") {
            $failMsg = "Condition is unexpectedly not TRUE.";
        }
        if($cond===true) {
            $this->passCount++;
            return true;
        } else {
            $this->failCount++;
            trigger_error($failMsg,$this->errorLevel);
            return false;
        }
    }

    /// Check that a condition is exactly strictly false.
    /// @param $cond - The condition to check.
    /// @param $failMsg - The message to display if the check fails.
    /// @return True if check ok, false if check failed.
     public function checkFalse($cond, string $failMsg="") : bool {
        if($failMsg=="") {
            $failMsg = "Condition is unexpectedly not FALSE.";
        }
        if($cond===false) {
            $this->passCount++;
            return true;
        } else {
            $this->failCount++;
            trigger_error($failMsg,$this->errorLevel);
            return false;
        }
    }

    /// Check that a condition is exactly strictly not true.
    /// @param $cond - The condition to check.
    /// @param $failMsg - The message to display if the check fails.
    /// @return True if check ok, false if check failed.
    public function checkNotTrue($cond, string $failMsg="") : bool {
        if($failMsg=="") {
            $failMsg = "Condition is unexpectedly TRUE.";
        }
        if($cond!==true) {
            $this->passCount++;
            return true;
        } else {
            $this->failCount++;
            trigger_error($failMsg,$this->errorLevel);
            return false;
        }
    }

    /// Check that a condition is exactly strictly not false.
    /// @param $cond - The condition to check.
    /// @param $failMsg - The message to display if the check fails.
    /// @return True if check ok, false if check failed.
    public function checkNotFalse($cond, string $failMsg="") : bool {
        if($failMsg=="") {
            $failMsg = "Condition is unexpectedly FALSE.";
        }
        if($cond!==false) {
            $this->passCount++;
            return true;
        } else {
            $this->failCount++;
            trigger_error($failMsg,$this->errorLevel);
            return false;
        }
    }

    /// Check that a condition is zero.
    /// @param $cond - The condition to check.
    /// @param $failMsg - The message to display if the check fails.
    /// @return True if check ok, false if check failed.
    public function checkIsZero($cond, string $failMsg="") : bool {
        if($failMsg=="") {
            $failMsg = "Condition is unexpectedly not an integer 0 (zero).";
        }
        if($cond===0) {
            $this->passCount++;
            return true;
        } else {
            $this->failCount++;
            trigger_error($failMsg,$this->errorLevel);
            return false;
        }
    }

    /// Check that a condition is null.
    /// @param $cond - The condition to check.
    /// @param $failMsg - The message to display if the check fails.
    /// @return True if check ok, false if check failed.
    public function checkIsNull($cond, string $failMsg="") : bool {
        if($failMsg=="") {
            $failMsg = "Condition is unexpectedly not NULL.";
        }
        if(is_null($cond)) {
            $this->passCount++;
            return true;
        } else {
            $this->failCount++;
            trigger_error($failMsg,$this->errorLevel);
            return false;
        }
    }

    /// Check that a condition is not null.
    /// @param $cond - The condition to check.
    /// @param $failMsg - The message to display if the check fails.
    /// @return True if check ok, false if check failed.
    public function checkIsNotNull($cond, string $failMsg="") : bool {
        if($failMsg=="") {
            $failMsg = "Condition is unexpectedly NULL.";
        }
        if(!is_null($cond)) {
            $this->passCount++;
            return true;
        } else {
            $this->failCount++;
            trigger_error($failMsg,$this->errorLevel);
            return false;
        }
    }

    /// Report that a check failed.
    /// @param $failMsg - The message to display.
    /// @return False.
    public function checkFailed(string $failMsg="") {
        if($failMsg=="") {
            $failMsg = "Check failed.";
        }
        $this->failCount++;
        trigger_error($failMsg,$this->errorLevel);
        return false;
    }

    /// Report that a check passed (updates counters).
    /// @return True.
    public function checkPassed() {
        $this->passCount++;
        return true;
    }

    # define some static functions that often help checking.

    /// Test if a variable in an associative array.
    /// @param $var - The variable to test.
    /// @return TRUE if $var is an associative array, else false.
    public static function isAssociativeArray($var) {
        if (!is_array($var)) {
            return false;
        }
        $compare = array_diff_key($var,array_keys(array_keys($var)));
        if (count($compare)==0) {
            return false;
        } else {
            return true;
        }
    }

    /// Describe a variable type and value(s) as a length limited string.
    /// @param $var - The variable to describe.
    /// @param $maxLength - Optional, the maximum length of the result.
    /// @return A string description of the variable, length limited.
    public static function describeVar($var, int $maxLength=80) : string {
        $result = "";
        if(is_array($var)) {
            if(self::isAssociativeArray($var)) {
                $result .= '{';
                $parts = array();
                foreach($var as $k => $v) {
                    $parts[] = self::describeVar($k) . '=>' . self::describeVar($v);
                }
                $result .= implode(",",$parts);
                $result .= '}';
            } else {
                $result .= '[';
                $parts = array();
                foreach($var as $v) {
                    $parts[] = self::describeVar($v,$maxLength);
                }
                $result .= implode(",",$parts);
                $result .= ']';
            }
        } else {
            $typ = gettype($var);
            if($typ=="boolean") {
                if($var===true) {
                    $typ .= "/true";
                } else {
                    $typ .= "/false";
                }
            }
            $str = strval($var);
            $result .= "($typ)'$str'";
        }
        $result = trim($result);
        if(strlen($result)>$maxLength) {
            $result = substr($result,0,$maxLength-3) . "...";
        }
        return $result;
    }
}
?>

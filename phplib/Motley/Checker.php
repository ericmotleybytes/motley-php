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
class Checker {

    protected $errorLevel = E_USER_ERROR;   ///< Error level to use for this instance.
    protected $failCount = 0;               ///< Number of check fails since reset.
    protected $passCount = 0;               ///< Number of check passes since reset.

    // Class constructor.
    // @param $errLvl - The php error level this instance will trigger us a check fails.
    // Multiple class instances can be instantiated at different error levels.
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
    /// @cond - The condition to check.
    /// @failMsg - The message to display if the check fails.
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
    /// @cond - The condition to check.
    /// @failMsg - The message to display if the check fails.
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
    /// @cond - The condition to check.
    /// @failMsg - The message to display if the check fails.
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
    /// @cond - The condition to check.
    /// @failMsg - The message to display if the check fails.
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
    /// @cond - The condition to check.
    /// @failMsg - The message to display if the check fails.
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
    /// @cond - The condition to check.
    /// @failMsg - The message to display if the check fails.
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
    /// @cond - The condition to check.
    /// @failMsg - The message to display if the check fails.
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
}
?>

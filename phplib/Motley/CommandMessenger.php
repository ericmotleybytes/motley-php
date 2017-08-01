<?php
/// Source code file for the Motley::Messenger class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\Checker;

/// Support message communications with end-users of command line utilities.
class CommandMessenger {
    const VERBO_MSG          = 'VERBO';  ///< Informational/verbose messages.
    const ERROR_MSG          = 'ERROR';  ///< Error/warning messages.
    const DEBUG_MSG          = 'DEBUG';  ///< Debug messages.
    const ERROR_NONE_LVL     = 0; ///< Error level for no error user messages.
    const ERROR_DIE_LVL      = 1; ///< Error level for die messages.
    const ERROR_ERROR_LVL    = 2; ///< Error level for error user messages.
    const ERROR_ALL_LVL      = 2; ///< Error level for error user messages.
    const ERROR_WARNING_LVL  = 3; ///< Error level for warning user messages.
    const VERBOSITY_NONE_LVL = 0; ///< Verbosity level for no informational user messages.
    const VERBOSITY_LOW_LVL  = 1; ///< Verbosity level for brief info user messages.
    const VERBOSITY_HIGH_LVL = 2; ///< Verbosity level for detailed info user messages.
    const VERBOSITY_ALL_LVL  = 2; ///< Verbosity level for detailed info user messages.
    const DEBUG_NONE_LVL     = 0; ///< Level for no debug messages.
    const DEBUG_LOW_LVL      = 1; ///< Level for brief debug messages.
    const DEBUG_HIGH_LVL     = 2; ///< Level for detailed debug messages.
    const DEBUG_ALL_LVL      = 2; ///< Level for detailed debug messages.
    const DEFAULT_ERROR_DEST = 'php://stderr'; ///< Default error message destination.
    const DEFAULT_VERBO_DEST = 'php://stdout'; ///< Default info message destination.
    const DEFAULT_DEBUG_DEST = 'php://stderr'; ///< Default debug message information.

    protected $minDieLvl   = self::ERROR_ERROR_LVL; ///< Min err lvl which exits program.
    protected $msgTypes    = array();        ///< Valid message types.
    protected $maxDispLvl  = array();        ///< Max message levels to display.
    protected $msgDest     = array();        ///< Message file destinations.
    protected $msgFH       = array();        ///< Message file handles.
    protected $chkE        = null;           ///< Motley\Checker error instance.
    protected $chkW        = null;           ///< Motley\Checker warning instance.

    /// Class instance constructor.
    public function __construct() {
        // initialize valid message type array.
        $this->msgTypes[] = self::VERBO_MSG;
        $this->msgTypes[] = self::ERROR_MSG;
        $this->msgTypes[] = self::DEBUG_MSG;
        // initialize max display level parameters
        $this->maxDebugLvl[self::VERBO_MSG] = self::VERBOSITY_LOW_LVL;
        $this->maxDebugLvl[self::ERROR_MSG] = self::ERROR_WARNING_LVL;
        $this->maxDebugLvl[self::DEBUG_MSG] = self::DEBUG_NONE_LVL;
        // initialize message destinations
        $this->msgDest[self::VERBO_MSG] = self::DEFAULT_VERBO_DEST;
        $this->msgDest[self::ERROR_MSG] = self::DEFAULT_ERROR_DEST;
        $this->msgDest[self::DEBUG_MSG] = self::DEFAULT_DEBUG_DEST;
        // initialize destination file handles entries.
        $this->msgFH[self::VERBO_MSG] = null;  // do not open until used first time.
        $this->msgFH[self::ERROR_MSG] = null;  // do not open until used first time.
        $this->msgFH[self::DEBUG_MSG] = null;  // do not open until used first time.
        // initialize the checker
        $this->chkE = new Checker(E_USER_ERROR);
        $this->chkW = new Checker(E_USER_WARNING);
    }

    /// Class instance destructor.
    function __destruct() {
        // close any open file handles
        foreach($this->msgTypes as $type) {
            if(!is_null($this->msgFH[$type])) {
                fclose($this->msgFH[$type]);
            }
        }
    }

    /// Generic user message display.
    /// @param $msgType - The type of message. See class constants
    /// VERBO_MSG, ERROR_MSG, DEBUG_MSG.
    /// @param $prefix - String prepended to $msg.
    /// @param $msg - The text message to display.
    /// @param $lvl - Message level for possible filtering.
    protected function genericMsg(
        string $msgType, string $prefix, string $msg, int $lvl) : bool {
        $chk = $this->chkE;
        # validate message type.
        $tst = in_array($msgType,$this->msgTypes);
        if(! $chk->checkTrue($tst)) { return false; }
        # check level
        $maxLvl = $this->maxDispLvl[$msgType];
        if($lvl > $maxLvl) {
            return false;
        }
        # prepend prefix
        $msg = "$prefix: $msg";
        # make sure message ends with line terminator.
        if(substr($msg,(0-strlen(PHP_EOL)))!=PHP_EOL) {
            $msg .= PHP_EOL;
        }
        # make sure file handle is open
        $dest = $this->msgDest[$msgType];
        $fh = $this->msgFH[$msgType];
        if(is_null($fh)) {
            $fh = fopen($dest,'a+');
            if(! $chk->checkNotFalse($fh)) { return false; }
            $this->msgFH[$msgType] = $fh;
        }
        # output message
        $byteCount = fwrite($fh,$msg);
        # check fwrite status
        if(! $chk->checkNotFalse($byteCount)) { return false; }
        return true;
    }

    /// Send an informational message to the end user for display or filtering.
    /// @param $message - The message.
    /// @return TRUE if message actually displayed, FALSE if filtered.
    public function infoMessage(string $message) : bool {
        $status = $this->genericMsg(self::VERBO_MSG,"INFO",
            $message,self::VERBOSITY_LOW_LVL);
        return $status;
    }

    /// Send a verbose info message to the end user for display or filtering.
    /// @param $message - The message.
    /// @return TRUE if message actually displayed, FALSE if filtered.
    public function verboseMessage(string $message) : bool {
        $status = $this->genericMsg(self::VERBO_MSG,"VERBOSE",
            $message,self::VERBOSITY_HIGH_LVL);
        return $status;
    }

    /// Send a warning message to the end user for display or filtering.
    /// @param $message - The message.
    /// @return TRUE if message actually displayed, FALSE if filtered.
    public function warningMessage(string $message) : bool {
        $status = $this->genericMsg(self::ERROR_MSG,"WARNING",
            $message,self::ERROR_WARNING_LVL);
        return $status;
    }

    /// Send an error message to the end user for display or filtering.
    /// By default, this will also terminate the program unless
    /// the setErrorExitLevel() function is used.
    /// @param $message - The message.
    /// @return TRUE if message actually displayed, FALSE if filtered.
    public function errorMessage(string $message) : bool {
        $status = $this->genericMsg(self::ERROR_MSG,"ERROR",
            $message,self::ERROR_ERROR_LVL);
        return $status;
    }

    /// Send a die message to the end user for display or filtering.
    /// By default, this will also terminate the program unless
    /// the setErrorExitLevel() function is used.
    /// @param $message - The message.
    /// @return TRUE if message actually displayed, FALSE if filtered.
    public function dieMessage(string $message) : bool {
        $status = $this->genericMsg(self::ERROR_MSG,"DIE",
            $message,self::ERROR_DIE_LVL);
        return $status;
    }

    /// Send a debug message to the end user for display or filtering.
    /// @param $message - The message.
    /// @return TRUE if message actually displayed, FALSE if filtered.
    public function debugMessage(string $message) : bool {
        $status = $this->genericMsg(self::DEBUG_MSG,"DEBUG",
            $message,self::DEBUG_LOW_LVL);
        return $status;
    }

    /// Send a verbose debug message to the end user for display or filtering.
    /// @param $message - The message.
    /// @return TRUE if message actually displayed, FALSE if filtered.
    public function verboseDebugMessage(string $message) : bool {
        $status = $this->genericMsg(self::DEBUG_MSG,"DEBUG",
            $message,self::DEBUG_HIGH_LVL);
        return $status;
    }

    /// Set the maximum displayed level of a message type.
    /// @param $msgType - The type of message.
    /// Must be one of the class constants
    /// VERBO_MSG, ERROR_MSG, or DEBUG_MSG.
    /// @param $lvl - The maximum display level.
    /// See the class constants for specific values to use.
    public function setDisplayLevel(string $msgType, int $lvl) {
        # validate message type.
        $chk = $this->chkE;
        $tst = in_array($msgType,$this->msgTypes);
        if(! $chk->checkTrue($tst,"Bad message type ($msgType).")) { return; }
        # set level
        $this->maxDispLvl[$msgType] = $lvl;
    }

    /// Get the maximum displayed level of a message type.
    /// @param $msgType - The type of message.
    /// Must be one of the class constants
    /// VERBO_MSG, ERROR_MSG, or DEBUG_MSG.
    /// @return The maximum display level.
    /// See the class constants for specific values used.
    public function getDisplayLevel(string $msgType) {
        # validate message type.
        $chk = $this->chkE;
        $tst = in_array($msgType,$this->msgTypes);
        if(! $chk->checkTrue($tst,"Bad message type ($msgType).")) { return null; }
        # get level
        return $this->maxDispLvl[$msgType];
    }

    /// Set the minimum error level that also causes program termination.
    /// @param $lvl - The minimum error level to also cause program exit.
    /// A non-zero exit status will be used.
    /// See the class constants for:
    /// ERROR_NONE_LVL, ERROR_DIE_LVL, ERROR_ERROR_LVL, ERROR_WARNING_LVL.
    public function setErrorExitLevel(int $lvl) {
        $this->minDieLvl = $lvl;
    }

    /// Get the minimum error level that also causes program termination.
    /// @return The minimum error level to also cause program exit.
    /// See the class constants for:
    /// ERROR_NONE_LVL, ERROR_DIE_LVL, ERROR_ERROR_LVL, ERROR_WARNING_LVL.
    public function getErrorExitLevel() : int {
        return $this->minDieLvl;
    }

    /// Set the new output destination file for a message type.
    /// @param $msgType - The type of message.
    /// Must be one of the class constants
    /// VERBO_MSG, ERROR_MSG, or DEBUG_MSG.
    /// @param $dest - The output destination file.
    /// See the class constants for default values.
    public function setOutputDestination(string $msgType, string $dest) {
        # validate message type.
        $chk = $this->chkE;
        $tst = in_array($msgType,$this->msgTypes);
        if(! $chk->checkTrue($tst,"Bad message type ($msgType).")) { return; }
        # close the old destination if needed
        if(!is_null($this->msgFH[$msgType])) {
            fclose($this->msgFH[$msgType]);
            $this->msgFH[$msgType] = null;
        }
        # replace the old destination
        $this->msgDest[$msgType] = $dest;
    }

    /// Get the output destination file for a message type.
    /// @param $msgType - The type of message.
    /// Must be one of the class constants
    /// VERBO_MSG, ERROR_MSG, or DEBUG_MSG.
    /// @return $dest - The output destination file.
    /// See the class constants for default values.
    public function getOutputDestination(string $msgType) : string {
        # validate message type.
        $chk = $this->chkE;
        $tst = in_array($msgType,$this->msgTypes);
        if(! $chk->checkTrue($tst,"Bad message type ($msgType).")) { return ""; }
        # return the destination
        return $this->msgDest[$msgType];
    }

}
?>

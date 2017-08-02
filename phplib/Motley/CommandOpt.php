<?php
/// Source code file for the Motley::CommandOpt class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\CommandArg;

/// Represent a command line option.
class CommandOpt extends CommandComponent {

    protected $optSwitches      = array();  ///< Option switch synonyms.
    protected $optArgument      = null;     ///< Option argument, if any.
    protected $optArgOptional   = false;    ///< Is option argument optional.

    /// Class instance constructor.
    /// @param $name - optional name for option.
    /// @param $desc - optional description for option.
    /// @param $switches - optional array of switches.
    public function __construct(string $name=null, string $desc=null,
        array $switches=null) {
        parent::__construct($name,$desc);
        if(!is_null($switches)) {
            $this->addOptSwitches($switches);
        }
    }

    /// Check option switch format.
    /// @param $switch - The switch to check.
    /// @return True is switch format is ok, else false.
    public function checkOptSwitch(string $switch) : bool {
        $pat1 = '/^\-[0-9a-zA-Z]{1}$/';
        $pat2 = '/^\-\-[0-9a-zA-Z]+[0-9a-zA-Z\_\-]*$/';
        if(preg_match($pat1,$switch)==1) {
            return true;
        } elseif (preg_match($pat2,$switch)==1) {
            return true;
        } else {
            return false;
        }
    }

    /// Add an array of switch synonyms.
    /// If a switch is already on the switch list, then
    /// it is ignored.
    /// @param $switches - An array of string values.
    /// @returns New number of switch synonyms defined.
    public function addOptSwitches(array $switches) : int {
        foreach($switches as $switch) {
            $check = $this->checkOptSwitch($switch);
            if (!$this->chkW->checkNotFalse($check,"Bad switch format ($switch).")) {
                continue;
            };
            if(!in_array($switch,$this->optSwitches)) {
                # only add non-duplicates
                $this->optSwitches[] = $switch;
            }
        }
        return count($this->optSwitches);
    }

    /// Get the list of switch synonyms.
    /// @return The list of switch synonyms.
    public function getOptSwitches() : array {
        return $this->optSwitches;
    }

    /// Clear all switch synonyms.
    public function clearOptSwitches() {
        $this->optSwitches = array();
    }

    /// Set the option argument, if any.
    /// @param $argObj - An instance of Motley::CommandArg.
    /// @param $isOpt - Is the argument portion of this option optional?
    public function setOptArg(CommandArg $argObj=null, bool $isOpt=false) {
        $this->optArgument    = $argObj;
        $this->optArgOptional = $isOpt;
    }

    /// Get the option argument, if any.
    /// @return An instance of Motley::CommandArg or null.
    public function getOptArg() {
        return $this->optArgument;
    }

    /// Get the option argument optional flag.
    /// @return True if the associated option argument is optional, else false;
    public function getOptArgOptional() {
        return $this->optArgOptional;
    }

    /// Get switches display string.
    /// @return A textual notation for the option.
    public function getSwitchesString() {
        $result = "";
        $switches = $this->optSwitches;
        $switches1 = array();  // one dash switches
        $switches2 = array();  // two dash switches
        foreach($switches as $switch) {
            if(substr($switch,0,2)=="--") {
                $switches2[] = $switch;
            } else {
                $switches1[] = $switch;
            }
        }
        $arg = $this->optArgument;
        $argName="";
        if(!is_null($arg)) {
            $argName = $arg->getDisplayName();
        }
        foreach($switches1 as $switch) {
            if (strlen($result)>0) {
                $result .= ",";  // add delimiter
            }
            $result .= $switch;
        }
        if(!is_null($arg) and count($switches2)==0) {
            # output option arg
            if($this->getOptArgOptional()) {
                $result .= " [" . $argName . "]";
            } else {
                $result .= " " . $argName;
            }
        }
        if (count($switches1)>0 and count($switches2)>0) {
            $result .= " | ";
        }
        $cnt=0;
        foreach($switches2 as $switch) {
            $cnt++;
            if ($cnt>1) {
                $result .= ",";  // add delimiter
            }
            $result .= $switch;
        }
        if (count($switches2)>0 and !is_null($arg)) {
            # output argument
            if($this->getOptArgOptional()) {
                $result .= "[=" . $argName . "]";
            } else {
                $result .= "=" . $argName;
            }
        }
        return $result;
    }

    /// Validate a command line option switch (and argument).
    /// @param $param - A command line parameter.
    /// @return TRUE if $param is valid, else FALSE.
    public function validate(string $param) : bool {
        if(substr($param,0,2)=="--") {
            // "--..." form
            $firstEquals = strpos($param,"=");
            if ($firstEquals!==false) {
                // found an equals sign
                $switchPart = substr($param,0,$firstEquals);
                $argPart = substr($param,$firstEquals+1);
            } else {
                // no equals sign found.
                $switchPart = $param;
                $argPart = null;
            }
        } elseif (substr($param,0,1)=="-") {
            // "-..." form
            $firstSpace = strpos($param," ");
            if ($firstSpace!==false) {
                // found a space
                $switchPart = substr($param,0,$firstSpace);
                $argPart = substr($param,$firstSpace+1);
            } else {
                $switchPart = $param;
                $argPart = null;
            }
        } else {
            // not a '-' or '--' switch
            $result = false;
            $message = "'$param' is a valid option switch.";
            $this->saveLastParam($param,$result,$message);
            return $result;
        }
        #echo("DBG: param='$param'\n");
        #echo("DBG: switchPart='$switchPart'\n");
        #echo("DBG: argPart='$argPart'\n");
        // validate $switchPart.
        $switchMatched = false;
        foreach($this->getOptSwitches() as $switch) {
            if ($switchPart==$switch) {
                $switchMatched = true;
                break;
            }
        }
        if ($switchMatched==false) {
            // switch part did not match.
            $result = false;
            $message = "'$param' does not match any switches expected here.";
            $this->saveLastParam($param,$result,$message);
            return $result;
        }
        if (!is_null($this->optArgument)) {
            // There is an associated arg, validate $argPart
            $arg = $this->optArgument;
            $argOpt = $this->optArgOptional;
            if($argOpt and is_null($argPart)) {
                $argPart = "-";  // shorthand for arg default value, if any.
            }
            if(is_null($argPart)) {
                $result = false;
                $message = "'$param' does not have a mandatory argument part.";
                $this->saveLastParam($param,$result,$message);
                return $result;
            }
            $result = $arg->validate($argPart);
            $params = array($switchPart,$arg->getLastParamValue());
            if($result) {
                $message = "'$param' switch and argument ok.";
                $this->saveLastParam($params,$result,$message);
                return $result;
            } else {
                $message = $arg->getLastParamMessage();
                $this->saveLastParam($params,$result,$message);
                return $result;
            }
        } else {
            // no associated arg, $argPart should be null.
            if(is_null($argPart)) {
                $result = true;
                $message = "'$param' switch ok.";
                $this->saveLastParam($param,$result,$message);
                return $result;
            } else {
                $result = false;
                $message = "'$param' has unexpected argument value.";
                $this->saveLastParam($param,$result,$message);
                return $result;
            }
        }
        // It should be impossible to reach here, but defensive code follows.
        // @codeCoverageIgnoreStart
        trigger_error("Unexpected path.",E_USER_ERROR);
        return false;
        // @codeCoverageIgnoreEnd
    }

}
?>

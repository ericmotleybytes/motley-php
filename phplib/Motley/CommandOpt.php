<?php
/// Class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

use Motley\GuidGenerator;
use Motley\CommandArg;

/// Represent a command line option.
class CommandOpt {

    protected $instanceGuid     = null;     ///< Unique guid for object instance.
    protected $optName          = "";       ///< Option name.
    protected $optDescription   = "";       ///< Option description.
    protected $optSwitches      = array();  ///< Option switch synonyms.
    protected $optArgument      = null;     ///< Option argument, if any.

    /// Class instance constructor.
    public function __construct(string $name=null, string $desc=null) {
        if(!is_null($name)) {
            $this->optName = $name;
        }
        if(!is_null($desc)) {
            $this->optDescription = $desc;
        }
        $guidGen = new GuidGenerator(false,false);
        $this->instanceGuid = $guidGen->generateGuid();
    }

    public function __clone() {
        // get a new instance guid for the cloned copy
        $guidGen = new GuidGenerator(false,false);
        $this->instanceGuid = $guidGen->generateGuid();
    }

    /// Get a newly instantiated instance copy. Safer and more flexible
    /// to use than the php built in 'clone' command.
    /// @param $name - new object argument name. If not specified or
    ///   if null, the opt name from the source object is unchanged.
    /// @param $desc - new object argument description. If not specified or
    ///   if null, the opt description from the source object is unchanged.
    public function copy(string $name=null, string $desc=null) : CommandOpt {
        $objCopy = clone $this;
        if(!is_null($name)) {
            $objCopy->setOptName($name);
        }
        if(!is_null($desc)) {
            $objCopy->setOptDescription($desc);
        }
        return $objCopy;
    }

    /// Set the option name.
    /// @param $name - the new option name.
    public function setOptName(string $name) {
        $this->optName = $name;
    }

    /// Get the option name.
    /// @return the current option name.
    public function getOptName() : string {
        return $this->optName;
    }

    /// Set the option description.
    /// @param $desc - the new option description.
    public function setOptDescription(string $desc) {
        $this->optDescription = $desc;
    }

    /// Get the option description.
    /// @return the current option description.
    public function getOptDescription() : string {
        return $this->optDescription;
    }

    /// Get the object instance GUID.
    /// @return The unique instance GUID.
    public function getInstanceGuid() : string {
        return $this->instanceGuid;
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
            if($check===false) {
                $msg = "Bad switch format ($switch).";
                trigger_error($msg,E_USER_WARNING);
                continue;
            }
            if(!in_array($switch,$this->optSwitches)) {
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
    public function setOptArg(CommandArg $argObj=null) {
        $this->optArgument = $argObj;
    }

    /// Get the option argument, if any.
    /// @return An instance of Motley::CommandArg or null.
    public function getOptArg() {
        return $this->optArgument;
    }
}
?>

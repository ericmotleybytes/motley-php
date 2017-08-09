<?php
/// Source code file for the Motley::CommandOptGrp class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\GuidGenerator;
use Motley\CommandOpt;

/// Represent a command line option group.
class CommandOptGrp extends CommandComponent {

    protected $options           = array();   ///< CommandOpt objects in group.
    protected $switches          = array();   ///< Switches from all options.
    protected $lastValidOption   = null;      ///< CommandOpt that passed validate.

    /// Class instance constructor.
    /// @param $name - The object name.
    /// @param $desc - The object description.
    public function __construct(string $name=null, string $desc=null) {
        if(!is_null($name)) {
            $name = "Options";
        }
        if(!is_null($desc)) {
            $desc = "Command line options.";
        }
        parent::__construct($name,$desc);
    }

    /// Add an option to the option group.
    /// If an option object with the same name already exists a warning
    /// is issued and the option object if not added to the group.
    /// @param $option - A CommandOpt object to add to the group.
    /// @returns New number of options in group.
    public function addOption(CommandOpt $option) : int {
        $optionName = $option->getName();
        foreach($this->options as $opt) {
            if($optionName==$opt->getName()) {
                $msg = "Option '$optionName' already in group.";
                trigger_error($msg,E_USER_WARNING);
                return count($this->options);
            }
        }
        $switches = array();
        foreach($option->getOptSwitches() as $switch) {
            if(in_array($switch,$this->switches)) {
                $msg = "Switch '$switch' already used in group.";
                trigger_error($msg,E_USER_WARNING);
                return count($this->options);
            }
            $switches[] = $switch;
        }
        $this->options[] = $option;
        $this->switches = array_merge($this->switches,$switches);
        return count($this->options);
    }

    /// Get the list of options in the group.
    /// @returns An array of CommandOpt objects in the group.
    public function getOptions() : array {
        return $this->options;
    }

    /// Clear the list of options in the group.
    public function clearOptions() {
        $this->options  = array();
        $this->switches = array();
    }
    
    /// Get a consolidated list of switches from all options in group.
    /// @return An array of all switches from all options.
    public function getSwitches() : array {
        $result = array();
        $options = $this->getOptions();
        foreach($options as $option) {
            $switches = $option->getOptSwitches();
            foreach($switches as $switch) {
                if(!in_array($switch,$result)) {
                    $result[] = $switch;
                }
            }
        }
        return $result;
    }

    /// Get the option associated with a switch (if any).
    /// @param $switch - A '-...' or '--...' command line switch.
    /// @return The CommandOpt object with the switch, or null if no matches.
    public function getOptionBySwitch(string $switch) {
        $result = null;
        $options = $this->getOptions();
        foreach($options as $option) {
            $switches = $option->getOptSwitches();
            foreach($switches as $s) {
                if ($s==$switch) {
                    $result = $option;
                    break 2;  // break from both loops
                }
            }
        }
        return $result;
    }

    /// Validate a switch parameter against a group of component options.
    /// This overrides the parent validate function.
    /// @param $param - A command line parameter(s).
    /// @return TRUE is trhe param if valid for one of the options in the group.
    /// Only one option should match because overlapping options (two options which
    /// use the same switch) are prevented when adding options to the group.
    public function validate(string $param) : bool {
        $result = false;
        $options = $this->getOptions();
        foreach($options as $option) {
            $status = $option->validate($param);
            if($status===true) {
                $result = true;
                $this->lastValidOption = $option;
                $message = "Option " . $option->getName() . " validated '$param'.";
                $this->saveLastParam($param,$result,$message);
                break;
            }
        }
        return $result;
    }

    /// Get the last option to pass a validate param test.
    public function getLastValidOption() {
        return $this->lastValidOption;
    }

}
?>

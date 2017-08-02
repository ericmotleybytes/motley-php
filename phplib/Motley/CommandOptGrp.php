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
class CommandOptGrp {

    protected $optGrpName        = "Options"; ///< Option group name.
    protected $optGrpDescription =
        "Command line options.";              ///< Option group description.
    protected $options           = array();   ///< CommandOpt objects in group.
    protected $switches          = array();   ///< Switches from all options.
    protected $displayName       = "";        ///< Display name for option group.

    /// Class instance constructor.
    public function __construct(string $name=null, string $desc=null) {
        if(!is_null($name)) {
            $this->optGrpName = $name;
        }
        if(!is_null($desc)) {
            $this->optGrpDescription = $desc;
        }
    }

    /// Set the option group name.
    /// @param $name - the option group name.
    public function setOptGrpName(string $name) {
        $this->optGrpName = $name;
    }

    /// Get the option group name.
    /// @return the current option group name.
    public function getOptGrpName() : string {
        return $this->optGrpName;
    }

    /// Set the option group description.
    /// @param $desc - the option group description.
    public function setOptGrpDescription(string $desc) {
        $this->optGrpDescription = $desc;
    }

    /// Get the option group description.
    /// @return the current option group description.
    public function getOptGrpDescription() : string {
        return $this->optGrpDescription;
    }

    /// Add an option to the option group.
    /// If an option object with the same name already exists a warning
    /// is issued and the option object if not added to the group.
    /// @param $option - A CommandOpt object to add to the group.
    /// @returns New number of options in group.
    public function addOption(CommandOpt $option) : int {
        $optionName = $option->getOptName();
        foreach($this->options as $opt) {
            if($optionName==$opt->getOptName()) {
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

    /// Set the option group display name for syntax help and so forth.
    /// @param $name - The option group display name.
    public function setDisplayName(string $name) {
        $this->displayName = $name;
    }

    /// Get the option group display name for syntax help and so forth.
    /// @returns The previously set display name, or the general name
    ///   if display name has not been explicitly set.
    public function getDisplayName() : string {
        $name = $this->displayName;
        if ($name == "") {
            $name = $this->optGrpName;
        }
        return $name;
    }
}
?>

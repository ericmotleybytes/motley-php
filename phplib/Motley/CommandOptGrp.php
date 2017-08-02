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

    /// Class instance constructor.
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

}
?>

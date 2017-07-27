<?php
/// Class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

use Motley\GuidGenerator;

/// Represent a command arrangement of options and arguments.
class CommandArrange {

    protected $arrName        = "Standard";         ///< Arrangement name.
    protected $arrDescription = "Standard usage.";  ///< Arrangement description.
    protected $displayName    = "";                 ///< Arrangement display name.
    protected $components     = array();            ///< CommandOptGrp and CommandArg list.

    /// Class instance constructor.
    public function __construct(string $name=null, string $desc=null) {
        if(!is_null($name)) {
            $this->arrName = $name;
        }
        if(!is_null($desc)) {
            $this->arrDescription = $desc;
        }
        $guidGen = new GuidGenerator(false,false);
        $this->instanceGuid = $guidGen->generateGuid();
    }

    /// Set the arrangement name.
    /// @param $name - the arrangement name.
    public function setArrangeName(string $name) {
        $this->arrName = $name;
    }

    /// Get the arrangement name.
    /// @return the arrangement name.
    public function getArrangeName() : string {
        return $this->arrName;
    }
    
    /// Set the arrangement description.
    /// @param $desc - the arrangement description.
    public function setArrangeDescription(string $desc) {
        $this->arrDescription = $desc;
    }

    /// Get the arrangement description.
    /// @return the arrangement description.
    public function getArrangeDescription() : string {
        return $this->arrDescription;
    }

    /// Set the arrangement display name for syntax help and so forth.
    /// @param $name - The arrangement display name.
    public function setDisplayName(string $name) {
        $this->displayName = $name;
    }

    /// Get the arrangement display name for syntax help and so forth.
    /// @returns The previously set display name, or the general name
    ///   if display name has not been explicitly set.
    public function getDisplayName() : string {
        $name = $this->displayName;
        if ($name == "") {
            $name = $this->arrName;
        }
        return $name;
    }

    /// Define a command line arrangement.
    /// @param $components - An array of CommandOptGrp and CommandArg objects.
    public function defineArrangement(array $components) {
        # make a pass to verify only CommandOptGrp or CommandArg objects.
        $validClassNames = array(
            "Motley\CommandOptGrp",
            "Motley\CommandOpt",
            "Motley\CommandArg"
        );
        foreach($components as $component) {
            $classOk = false;
            foreach($validClassNames as $validClassName) {
                if(is_a($component,$validClassName)) {
                    $classOk = true;
                    break;
                }
            }
            if(!$classOk) {
                $msg = "Class " . get_class($component);
                $msg = $msg . " is not in " . implode(" ",$validClassNames);
                trigger_error($msg,E_USER_WARNING);
                return;
            }
        }
        # make a final pass to save components
        foreach($components as $component) {
            $this->components[] = $component;
        }
    }

    /// Get the currently defined arrangement array.
    /// @returns The arrangement component array of option groups, options, and args.
    public function getArrangement() {
        return $this->components;
    }

    /// Clear the arrangement component array.
    public function clearArrangement() {
        $this->components = array();
    }
}
?>

<?php
/// Source code file for the Motley::CommandComponent class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\Checker;

/// Represent a component of a command line arrangement.
class CommandComponent {

    protected $name        = "";     ///< Object name.
    protected $description = "";     ///< Object description.
    protected $displayName = null;   ///< Object display name.
    protected $lastParamValue   = "";    ///< The last param value.
    protected $lastParamIsValid = false; ///< Was the last param valid.
    protected $lastParamMessage = "";    ///< Possible error message about last param.
    protected $validParamHist = array(); ///< All valid params validated since reset.
    protected $chkW             = null;  ///< Warning checker.
    protected $chkE             = null;  ///< Error checker.

    /// Class instance constructor.
    /// @param $name - The object name.
    /// @param $desc - The object description.
    public function __construct(string $name=null, string $desc=null) {
        if(!is_null($name)) {
            $this->name = $name;
        }
        if(!is_null($desc)) {
            $this->description = $desc;
        }
        $this->chkW = new Checker(E_USER_WARNING);
        $this->chkE = new Checker(E_USER_ERROR);
    }

    /// Get the object name.
    /// @return the object name.
    public function getName() : string {
        return $this->name;
    }

    /// Set the object name.
    /// @param $name - The object name.
    public function setName(string $name) {
        $this->name = $name;
    }

    /// Get the object description.
    /// @return the object description.
    public function getDescription() : string {
        return $this->description;
    }

    /// Set the object description.
    /// @param $desc - the object description.
    public function setDescription(string $desc) {
        $this->description = $desc;
    }

    /// Get the display name.
    /// @return the display name.
    public function getDisplayName() : string {
        if(!is_null($this->displayName)) {
            return $this->displayName;
        } else {
            return $this->name;
        }
    }

    /// Set the display name.
    /// @param $dispName - The display name.
    public function setDisplayName($dispName) {
        $this->displayName = $dispName;
    }

    /// Save the last command line parameter processed.
    /// @param $param - The command line parameter.
    /// @param $isValid - True if $param meets validation requirements.
    /// @param $message - Explain why $param is or is not valid.
    protected function saveLastParam($param, bool $isValid, string $message) {
        $this->lastParamValue   = $param;
        $this->lastParamIsValid = $isValid;
        if($isValid) {
            $this->validParamHist[] = $param;
            $this->lastParamMessage = "VALID: $message";
        } else {
            $this->lastParamMessage = "INVALID: $message";
        }
    }

    /// get the last param value.
    /// @return The last command line parameter processed.
    public function getLastParamValue() {
        return $this->lastParamValue;
    }

    /// get the last param is valid flag.
    /// @return The last command line parameter is valid flag.
    public function getLastParamIsValid() : bool {
        return $this->lastParamIsValid;
    }

    /// get the last param message.
    /// @return The last command line parameter message.
    public function getLastParamMessage() : string {
        return $this->lastParamMessage;
    }

    /// Validate a command line parameter.
    /// @param $param - A command line parameter.
    /// @return TRUE if $param is valid, else FALSE.
    public function validate(string $param) : bool {
        $result  = false;
        $message = "All params invalid until child overrides validate function!";
        $this->saveLastParam($param,$result,$message);
        return $result;
    }

    /// Get the valid params history.
    /// @return Array of all valid params validated since last reset.
    public function getValidParamHistory() {
        return $this->validParamHist;
    }

    /// Reset the valid params history.
    public function resetValidParamHistory() {
        $this->validParamHist = array();
    }

    // Static functions...

    /// Find component(s) by name from an array of Motley::CommandComponent objects.
    /// @param $name - The name of the component.
    /// @param $components - An array of Motley::CommandComponent objects.
    /// This includes Motley::CommandArg, Motley::CommandOpt, Motley::CommandOptGrp,
    /// and Motley::CommandDoubleDash.
    /// @return An array of components that matched the name.
    public static function findComponentByName(string $name, array $components) : array {
        $result = array();
        foreach ($components as $component) {
            if($name==$component->getName()) {
                $result[] = $component;
            }
        }
        return $result;
    }

}
?>

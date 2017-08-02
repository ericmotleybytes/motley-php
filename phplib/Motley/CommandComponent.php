<?php
/// Source code file for the Motley::CommandComponent class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

/// Represent a component of a command line arrangement.
class CommandComponent {

    protected $name        = "";     ///< Object name.
    protected $description = "";     ///< Object description.
    protected $displayName = null;   ///< Object display name.
    protected $lastParamValue   = "";    ///< The last param value.
    protected $lastParamIsValid = false; ///< Was the last param valid.
    protected $lastParamMessage = "";    ///< Possible error message about last param.

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
    protected function saveLastParam(string $param, bool $isValid, string $message) {
        $this->lastParamValue   = $param;
        $this->lastParamIsValid = $isValid;
        $this->lastParamMessage = $message;
    }

    /// get the last param value.
    /// @return The last command line parameter processed.
    public function getLastParamValue() : string {
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
    public function validate(string $param) : bool {
        $result  = false;;
        $message = "All params invalid until child overrides validate function!";
        $this->saveLastParam($param,$result,$message);
        return $result;
    }

}
?>

<?php
/// Source code file for the Motley::CommandDoubleDash class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\CommandComponent;

/// Represent a command line bare double-dash ('--').
/// This is traditionally used during command line parsing to indicate
/// that no more -x or -xxx options switches follow, only positional
/// arguments.
class CommandDoubleDash extends CommandComponent {

    /// Class instance constructor.
    /// @param $name - The object name.
    /// @param $desc - The object description.
    public function __construct(string $name=null, string $desc=null) {
        if(is_null($name)) { $name = "--"; }
        if(is_null($desc)) { $desc = "A bare double-dash."; }
        parent::__construct($name,$desc);
        $this->displayName = "--";
    }

    /// Validate a double dash.
    /// @param $param - A command line parameter.
    /// @return TRUE if $param is valid, else FALSE.
    public function validate(string $param) : bool {
        if($param=="--") {
            $result  = true;
            $message = "'$param' is a valid double dash.";
        } else {
            $result = false;
            $message = "'$param' is not a valid double dash.";
        }
        $this->saveLastParam($param,$result,$message);
        return $result;
    }

}
?>

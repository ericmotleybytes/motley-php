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
    public function __construct(string $name=null, string $desc=null) {
        if(is_null($name)) { $name = "--"; }
        if(is_null($desc)) { $desc = "A bare double-dash."; }
        parent::__construct($name,$desc);
        $this->displayName = "--";
    }

}
?>

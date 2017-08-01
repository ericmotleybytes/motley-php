<?php
/// Source code file for the CommandDemo class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\Command;
use Motley\CommandOpt;
use Motley\CommandArrange;

/// Demonstrates creating a command line program using Motley Command classes.
class CommandDemo extends Command {

    /// Class constructor.
    /// @param $name - The name of the command.
    /// @param $desc - A description of the command.
    public function __construct(string $name, string $desc) {
        parent::__construct($name,$desc);
        # define a help option and associated command arrangement.
        $helpOpt = new CommandOpt("Help","Display help screen.");
        $helpOpt->addOptSwitches(array("-h","--help"));
        $helpArran = new CommandArrange("HelpArrangement","Provides help.");
        $helpArran->addOpt($helpOpt,true,false);  # optional makes it default
        # define a version option and associated command arrangement.
        $versOpt = new CommandOpt("Version","Display program version.");
        $versOpt->addOptSwitches(array("-v","--version"));
        $versArran = new CommandArrange("VersArrangement","Display version.");
        $versArran->addOpt($versOpt,false,false);
        # add arrangements to the command.
        $this->addArrangement($helpArran);
        $this->addArrangement($versArran);
    }
    public function run(array $argv, bool $exit=true) : int {
        echo "Running Motley\\CommandDemo...\n";
        $idx=0;
        foreach($argv as $arg) {
            echo "argv[$idx]='$argv[$idx]'.\n";
            $idx++;
        }
        $this->infoMessage("This is a message.");
        return 0;
    }
}
?>

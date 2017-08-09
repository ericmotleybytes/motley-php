<?php
/// Source code file for the Motley::CommandDemo class.
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
    const HelpArranName = "HelpArrangement";  ///< Identifying name for help arrangement.
    const VersArranName = "VersArrangement";  ///< Identifying name for version arrangement.
    const Version       = "0.5.0";            ///< Program version.

    /// Class constructor.
    /// @param $name - The name of the command.
    /// @param $desc - A description of the command.
    public function __construct(string $name, string $desc) {
        parent::__construct($name,$desc);
        # define a help option and associated command arrangement.
        $helpOpt = new CommandOpt("Help","Display help screen.");
        $helpOpt->addOptSwitches(array("-h","--help"));
        $helpArran = new CommandArrange(self::HelpArranName,"Provides help.");
        $helpArran->addComponent($helpOpt,false,false);
        # define a version option and associated command arrangement.
        $versOpt = new CommandOpt("Version","Display program version.");
        $versOpt->addOptSwitches(array("-v","--version"));
        $versArran = new CommandArrange(self::VersArranName,"Display version.");
        $versArran->addComponent($versOpt,false,false);
        # add arrangements to the command.
        $this->addArrangement($helpArran);
        $this->addArrangement($versArran);
    }

    /// Run the command.
    /// @param $exit - If TRUE, the process exits after running this function.
    /// @return An integer status code, zero indicates success. If $exit is
    /// set to TRUE this status code becomes the process exit status.
    public function run(bool $exit=true) : int {
        $argv = $this->getArgv();
        if(count($argv)==0) {
            // ignore coverage metrics for this defensive code.
            // @codeCoverageIgnoreStart
            $argv[] = $this->getDisplayName();  // use display name as program name
            // @codeCoverageIgnoreEnd
        }
        if(count($argv)==1) {
            $argv[] = "--help";                 // default no args to help
            $this->setArgv($argv);
        }
        $msg = $this->getMessenger();
        $msg->setErrorExitLevel($msg::ERROR_DIE_LVL);
        $matchingArrangements = $this->parse();
        if (count($matchingArrangements)==0) {
            $statcode = 1;
            $msg->errorMessage("Unmatched command line arguments.");
            if ($exit) {
                // ignore coverage metrics for non-unit-test code.
                // @codeCoverageIgnoreStart
                exit($statcode);
                // @codeCoverageIgnoreEnd
            } else {
                return $statcode;
            }
        }
        $statcode = 0;
        $arran = $matchingArrangements[0];  // use first matching arrangement
        $arranName = $arran->getName();
        if($arranName==self::HelpArranName) {
            # display help
            $this->displayHelp();
        } elseif($arranName==self::VersArranName) {
            # display version
            $vline = $this->getArgv()[0] . " - version " . self::Version;
            echo($vline . PHP_EOL);
        }
        if ($exit) {
            // ignore coverage metrics for non-unit-test code.
            // @codeCoverageIgnoreStart
            exit($statcode);
            // @codeCoverageIgnoreEnd
        } else {
            return $statcode;
        }
    }
}
?>

<?php
/// Source code file for the Motley::Command class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\CommandArg;
use Motley\CommandOpt;
use Motley\CommandOptGrp;
use Motley\CommandDoubleDash;
use Motley\CommandArrange;
use Motley\CommandMessenger;
use Motley\UsageFormatter;

/// Represent a command line option.
class Command {

    protected $cmdName          = "";        ///< Command name.
    protected $cmdDescription   = "";        ///< Command description.
    protected $cmdArrangements  = array();   ///< Command opt/arg layouts.
    protected $displayName      = "";        ///< Command display name.
    protected $userMessenger    = null;      ///< CommandMessage object instance.

    /// Class instance constructor.
    /// @param $name - Command instance name.
    /// @param $desc - Command description.
    public function __construct(string $name=null, string $desc=null) {
        if(!is_null($name)) {
            $this->cmdName = $name;
        }
        if(!is_null($desc)) {
            $this->cmdDescription = $desc;
        }
        $this->userMessenger = new CommandMessenger();
    }

    /// Set the command name.
    /// @param $name - the command name.
    public function setCmdName(string $name) {
        $this->cmdName = $name;
    }

    /// Get the command name.
    /// @return the current command name.
    public function getCmdName() : string {
        return $this->cmdName;
    }

    /// Set the command description.
    /// @param $desc - the command description.
    public function setCmdDescription(string $desc) {
        $this->cmdDescription = $desc;
    }

    /// Get the command description.
    /// @return the current command description.
    public function getCmdDescription() : string {
        return $this->cmdDescription;
    }

    /// Set the command display name for syntax help and so forth.
    /// @param $name - The command display name.
    public function setDisplayName(string $name) {
        $this->displayName = $name;
    }

    /// Get the command display name for syntax help and so forth.
    /// @returns The previously set display name, or the general name
    ///   if display name has not been explicitly set.
    public function getDisplayName() : string {
        $name = $this->displayName;
        if ($name == "") {
            $name = $this->cmdName;
        }
        return $name;
    }

    /// Add command arrangement of option groups and arguments.
    /// @param $arrange - The command arrangement.
    public function addArrangement(CommandArrange $arrange) {
        $this->cmdArrangements[] = $arrange;
    }

    /// Get all command arrangements.
    /// @returns All command arrangements.
    public function getArrangements() : array {
        return $this->cmdArrangements;
    }

    /// Clear all command arrangements.
    public function clearArrangements() {
        $this->cmdArrangements = array();
    }

    /// Get textual help information.
    public function getHelp() : string {
        $masterArgList = array();
        $masterOptList = array();
        $masterOptGrpList = array();
        $fmt = new UsageFormatter();
        $colWidth = $fmt->getColumnWidth();
        $descCol = (int)($colWidth/3);
        $descCol = min($descCol,40);
        $descCol = max($descCol,4);
        $fmt->setLeftIndent(0);
        $fmt->setRightIndent(0);
        $fmt->setContinueIndent(2);
        $fmt->formatChunk("Name:");
        $fmt->formatBreak();
        $fmt->setLeftIndent(2);
        $fmt->formatChunk($this->cmdName . " -");
        $fmt->formatText($this->cmdDescription);
        $fmt->formatBreak();
        $fmt->setLeftIndent(0);
        $fmt->formatChunk("Usage:");
        $fmt->formatBreak();
        $fmt->setLeftIndent(2);
        foreach($this->cmdArrangements as $arrangement) {
            $fmt->formatChunk($this->getDisplayName());
            $components = $arrangement->getComponents();
            foreach($components as $component) {
                $obj = $component[CommandArrange::OBJ_KEY];
                $opt = $component[CommandArrange::OPT_KEY];
                $rep = $component[CommandArrange::REP_KEY];
                if(is_a($obj,"\Motley\CommandArg")) {
                    $masterArgList[] = $obj;
                    $argString = $obj->getDisplayName();
                    if($rep) {
                        $argString .= " ...";
                    }
                    if($opt) {
                        $argString = '[' . $argString . ']';
                    }
                    $fmt->formatChunk($argString);
                } elseif(is_a($obj,"\Motley\CommandOpt")) {
                    $masterOptList[] = $obj;
                    $switchesString = $obj->getSwitchesString();
                    if($opt) {
                        $switchesString = '[' . $switchesString . ']';
                    }
                    $fmt->formatChunk($switchesString);
                } elseif(is_a($obj,"\Motley\CommandOptGrp")) {
                    $masterOptGrpList[] = $obj;
                    $grpString = $obj->getDisplayName();
                    if($opt) {
                        $grpString = '[' . $grpString . ']';
                    }
                    $fmt->formatChunk($grpString);
                } elseif(is_a($obj,"\Motley\CommandDoubleDash")) {
                    $ddString = $obj->getDisplayName();
                    if($opt) {
                        $ddString = '[' . $ddString . ']';
                    }
                    $fmt->formatChunk($ddString);
                }
            }
            $fmt->formatBreak();
        }
        # output argument help (if any)
        if(count($masterArgList)>0) {
            $fmt->setLeftIndent(0);
            $fmt->formatChunk("Arguments:");
            $fmt->formatBreak();
            $fmt->setLeftIndent(2);
            foreach($masterArgList as $arg) {
                $fmt->formatChunk($arg->getDisplayName());
                $fmt->formatText($arg->getArgDescription(),$descCol);
                $fmt->formatBreak();
            }
        }
        # output option group help (if any)
        if(count($masterOptGrpList)>0) {
            foreach($masterOptGrpList as $optGrp) {
                $fmt->setLeftIndent(0);
                $fmt->formatChunk($optGrp->getDisplayName() . ":");
                $fmt->formatBreak();
                $fmt->setLeftIndent(2);
                $opts = $optGrp->getOptions();
                foreach($opts as $opt) {
                    $switchesString = $opt->getSwitchesString();
                    $fmt->formatChunk($switchesString);
                    $desc = $opt->getOptDescription();
                    $fmt->formatText($desc,$descCol);
                    $fmt->formatBreak();
                }
            }
        }
        # output options not in an option group
        if(count($masterOptList)>0) {
            $fmt->setLeftIndent(0);
            $fmt->formatChunk("OtherOptions:");
            $fmt->formatBreak();
            $fmt->setLeftIndent(2);
            foreach($masterOptList as $opt) {
                $switchesString = $opt->getSwitchesString();
                $fmt->formatChunk($switchesString);
                $desc = $opt->getOptDescription();
                $fmt->formatText($desc,$descCol);
                $fmt->formatBreak();
            }
        }
        return $fmt->getFormattedText();
    }

    /// Display help information to standard output.
    /// See Motley::Command::getHelp for legal values.
    public function displayHelp() {
        echo($this->getHelp());
    }

    /// Get the user messenger object.
    public function getMessenger() {
        return $this->userMessenger;
    }

    /// Try to parse all defined arrangements against command line arguments.
    /// @param $argv - The command line argument array.
    /// @return An array of arrangements that match command line arguments.
    public function parse(array $argv) : array {
        $result = array();
        foreach($this->cmdArrangements as $arrangement) {
            $matched = $arrangement->parse($argv);
            if ($matched===true) {
                $result[] = $arrangement;
            }
        }
        return $result;
    }

    /// Run a command using command line arguments.
    /// @param $argv - The command line argument array.
    /// @param $exit - If TRUE, the function exits the program with a statcode.
    /// @return The final status code integer value, unless $exit.
    public function run(array $argv, bool $exit=true) : int {
        $matches = $this->parse($argv);
        if (count($matches)>0) {
            $statcode = 0;
        } else {
            $statcode = 1;
        }
        if($exit) exit($statcode); else return $statcode;
    }
}
?>

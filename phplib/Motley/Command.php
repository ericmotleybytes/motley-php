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

    protected $name             = "";        ///< Command name.
    protected $description      = "";        ///< Command description.
    protected $arrangements     = array();   ///< Command opt/arg layouts.
    protected $displayName      = "";        ///< Command display name.
    protected $userMessenger    = null;      ///< CommandMessage object instance.
    protected $formatter        = null;      ///< UsageFormatter instance.
    protected $argv             = null;      ///< Initialized to global $argv.

    /// Class instance constructor.
    /// @param $name - Command instance name.
    /// @param $desc - Command description.
    public function __construct(string $name=null, string $desc=null) {
        global $argv;
        $this->argv = $argv;
        if(!is_null($name)) {
            $this->name = $name;
        }
        if(!is_null($desc)) {
            $this->description = $desc;
        }
    }

    /// Set the command name.
    /// @param $name - the command name.
    public function setName(string $name) {
        $this->name = $name;
    }

    /// Get the command name.
    /// @return the current command name.
    public function getName() : string {
        return $this->name;
    }

    /// Set the command description.
    /// @param $desc - the command description.
    public function setDescription(string $desc) {
        $this->description = $desc;
    }

    /// Get the command description.
    /// @return the current command description.
    public function getDescription() : string {
        return $this->description;
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
            $name = $this->name;
        }
        return $name;
    }

    /// Set what the Command object uses as command line parameters.
    /// Note that this is initialized to the global $argv array during instantiation.
    /// @param $cmdv - An array of script name followed by command arguments.
    public function setArgv(array $cmdv=array()) {
        global $argv;
        if(count($cmdv)==0) {
            if($argv!==null) {
                $this->argv = array($argv[0]);
            } else {
                $this->argv = array($this->getDisplayName());
            }
        } else {
            $this->argv = $cmdv;
        }
    }

    /// Get what the Command object is using as command line arguments.
    /// @return As array of script name followed by command line arguments.
    public function getArgv() {
        return $this->argv;
    }

    /// Add command arrangement of option groups and arguments.
    /// @param $arrange - The command arrangement.
    public function addArrangement(CommandArrange $arrange) {
        $this->arrangements[] = $arrange;
    }

    /// Get all command arrangements.
    /// @returns All command arrangements.
    public function getArrangements() : array {
        return $this->arrangements;
    }

    /// Clear all command arrangements.
    public function clearArrangements() {
        $this->arrangements = array();
    }

    /// Get the user messenger object.
    public function getMessenger() : CommandMessenger {
        if (is_null($this->userMessenger)) {
            $this->userMessenger = new CommandMessenger();
        }
        return $this->userMessenger;
    }

    /// Get the usage formatter object.
    public function getUsageFormatter() : UsageFormatter {
        if (is_null($this->formatter)) {
            $this->formatter = new UsageFormatter();
        }
        return $this->formatter;
    }

    /// Get textual help information.
    public function getHelp() : string {
        $masterArgList = array();
        $masterOptList = array();
        $masterOptGrpList = array();
        $fmt = $this->getUsageFormatter();
        $fmt->clear();
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
        $fmt->formatChunk($this->name . " -");
        $fmt->formatText($this->description);
        $fmt->formatBreak();
        $fmt->setLeftIndent(0);
        $fmt->formatChunk("Usage:");
        $fmt->formatBreak();
        $fmt->setLeftIndent(2);
        foreach($this->arrangements as $arrangement) {
            $fmt->formatChunk($this->getDisplayName());
            $aComps = $arrangement->getArranComps();
            foreach($aComps as $aComp) {
                $comp  = $aComp->getCompObj();
                $isOpt = $aComp->getIsOptional();
                $isRep = $aComp->getIsRepeatable();
                if(is_a($comp,"\Motley\CommandArg")) {
                    $masterArgList[] = $comp;
                    $argString = $comp->getDisplayName();
                    if($isRep) {
                        $argString .= " ...";
                    }
                    if($isOpt) {
                        $argString = '[' . $argString . ']';
                    }
                    $fmt->formatChunk($argString);
                } elseif(is_a($comp,"\Motley\CommandOpt")) {
                    $masterOptList[] = $comp;
                    $switchesString = $comp->getSwitchesString();
                    if($isOpt) {
                        $switchesString = '[' . $switchesString . ']';
                    }
                    $fmt->formatChunk($switchesString);
                } elseif(is_a($comp,"\Motley\CommandOptGrp")) {
                    $masterOptGrpList[] = $comp;
                    $grpString = $comp->getDisplayName();
                    if($isOpt) {
                        $grpString = '[' . $grpString . ']';
                    }
                    $fmt->formatChunk($grpString);
                } elseif(is_a($comp,"\Motley\CommandDoubleDash")) {
                    $ddString = $comp->getDisplayName();
                    if($isOpt) {
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
                $fmt->formatText($arg->getDescription(),$descCol);
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
                    $desc = $opt->getDescription();
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
                $desc = $opt->getDescription();
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

    /// Try to parse all defined arrangements against command line arguments.
    /// @return An array of arrangements that match command line arguments.
    public function parse() : array {
        $argv = $this->argv;
        $result = array();
        foreach($this->arrangements as $arrangement) {
            $matched = $arrangement->parse($argv);
            if ($matched===true) {
                $result[] = $arrangement;
            }
        }
        return $result;
    }

    /// Run a command using command line arguments.
    /// @param $exit - If TRUE, the function exits the program with a statcode.
    /// @return The final status code integer value, unless $exit.
    public function run(bool $exit=true) : int {
        $argv = $this->argv;
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

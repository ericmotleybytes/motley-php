<?php
/// Source code file for the Motley::CommandArrange class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\GuidGenerator;
use Motley\CommandArg;
use Motley\CommandOpt;
use Motley\CommandOptGrp;
use Motley\CommandDoubleDash;

/// Represent a command arrangement of options and arguments.
class CommandArrange {
    const OBJ_KEY = "obj";  ///< hash array key for object.
    const OPT_KEY = "opt";  ///< hash array key for optional flag.
    const REP_KEY = "rep";  ///< hash array key for repeatable flag.

    protected $arrName        = "Standard";         ///< Arrangement name.
    protected $arrDescription = "Standard usage.";  ///< Arrangement description.
    protected $displayName    = "";                 ///< Arrangement display name.
    /// Components of the arrangement. This is a simple array of hash arrays.
    /// The hash arrays have three entries each: "obj" for the instantiated
    /// CommandArg, CommandOpt, CommandOptGrp. or CommandDoubleDash  object;
    // "opt" for a boolean optional flag; and "rep" for a boolean repeatable flag.
    /// A repeatable CommandArg can repeat on the command line.
    /// A repeatable CommandOpt can be repeated on the command line.
    protected $components = array();
    protected $argv       = array();  ///< Filled with command line args when parsing.
    private   $nextCompIdx    = 0;    // Index into $components.
    private   $nextArgvIdx    = 1;    // Index into $argv (skip entry 0).
    private   $matchedComps   = array(); // Matched portion of $components.
    private   $matchedArgvs   = array(); // Matched portion of $argv.
    private   $matched        = false;   // True if $argv matched $components.
    private   $mismatchReason = "";      // Reason for mismatch, if any.

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

    /// Add a component to the arrangement.
    /// @param $obj - CommandArg, CommandOpt,CommandOptGrp, or CommandDoubleDash.
    /// @param $optional - True for optional.
    /// @param $repeatable - True for repeatable.
    private function addComponent($obj, bool $optional, bool $repeatable) {
        $component = array();
        $component[self::OBJ_KEY] = $obj;
        $component[self::OPT_KEY] = $optional;
        $component[self::REP_KEY] = $repeatable;
        $this->components[] = $component;
    }

    /// Add a CommandArg to the arrangement.
    /// @param $obj - A Motley::CommandArg object.
    /// @param $optional - True for optional.
    /// @param $repeatable - True for repeatable.
    public function addArg(
        CommandArg $obj, bool $optional=false, bool $repeatable=false) {
        $this->addComponent($obj, $optional, $repeatable);
    }

    /// Add a CommandOpt to the arrangement.
    /// @param $obj - A Motley::CommandOpt object.
    /// @param $optional - True for optional.
    /// @param $repeatable - True for repeatable.
    public function addOpt(
        CommandOpt $obj, bool $optional=false, bool $repeatable=false) {
        $this->addComponent($obj, $optional, $repeatable);
    }

    /// Add a CommandOptGrp to the arrangement.
    /// @param $obj - A Motley::CommandOptGrp object.
    /// @param $optional - True for optional.
    /// @param $repeatable - True for repeatable.
    public function addOptGrp(
        CommandOptGrp $obj, bool $optional=true, bool $repeatable=false) {
        $this->addComponent($obj, $optional, $repeatable);
    }

    /// Add a bare double dash ('--') to the arrangement.
    /// @param $obj - A Motley::CommandDoubleDash object.
    /// @param $optional - True for optional.
    public function addDoubleDash(
        CommandDoubleDash $obj, bool $optional=true) {
        $this->addComponent($obj, $optional, false);
    }

    /// Get the arrangement component array.
    /// @returns The arrangement component array of option groups, options, and args.
    public function getComponents() {
        return $this->components;
    }

    /// Clear the arrangement component array.
    public function clearComponents() {
        $this->components = array();
    }

    /// Try parsing command lines arguments.
    /// @param The command line argument array.
    /// @return TRUE if command line argement match arrangement, else false.
    public function parse(array $argv) : bool {
        $this->rewindParsing();
        $ddFound = false;  // has "--" been found yet?
        while(($param=$this->getNextArgv())!==false) {
            $paramMatched = false;
            while(($comp=$this->getNextComponent())!==false) {
                // check for double dash
                #if(is_a($comp,CommandDoubleDash)) {
                #    $ddFound = true;
                #    continue;  // get next component
                #}
                # TBD
                
            }
            if($paramMatched===false) {
                $this->mismatchReason = "Unexpected parameter '$param'.";
                break;
            }
        }
        return $this->matched;
    }

    private function rewindParsing() {
        $this->matched        = false;
        $this->nextCompIdx    = 0;
        $this->nextArgvIdx    = 1;
        $this->matchedComps   = array();
        $this->matchedArgvs   = array($this->argv[0]);
        $this->mismatchReason = "";
    }

    private function getNextComponent() {
        if($this->nextCompIdx < count($this->components)) {
            $result = $this->components[$this->nextCompIdx];
            $this->nextCompIdx++;
        } else {
            $result = false;
        }
        return $result;
    }

    private function getNextArgv() {
        if($this->nextArgvIdx < count($this->argv)) {
            $result = $this->argv[$this->nextArgvIdx];
            $this->nextArgvIdx++;
        } else {
            $result = false;
        }
        return $result;
    }

    private function remainingCompCount() : int {
        $result = count($this->components) - $this->nextCompIdx;
        $result = max(0,$result);
        return $result;
    }

    private function remainingArgvCount() : int {
        $result = count($this->argv) - $this->nextArgvIdx;
        $result = max(0,$result);
        return $result;
    }

}
?>

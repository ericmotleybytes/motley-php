<?php
/// Source code file for the Motley::CommandArrange class.
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
use Motley\CommandArrangeComp;
use Motley\Checker;

/// Represent a command arrangement of options and arguments.
class CommandArrange {

    protected $name           = "Standard";         ///< Arrangement name.
    protected $description    = "Standard usage.";  ///< Arrangement description.
    protected $displayName    = "";                 ///< Arrangement display name.
    protected $arranComps = array();  ///< Array of Motley::CommandArrangeComp objects.
    private   $nextCompIdx    = 0;       // Index into $components.
    private   $argv           = array(); // command args used by parse
    private   $nextArgvIdx    = 1;       // Index into $argv (skip entry 0).
    private   $matchingComps  = array(); // Matching args and options.
    private   $matched        = false;   // True if $argv matched $components.
    private   $matchingReason = "";      // Reason for match or mismatch.
    private   $chkE           = null;    // error level Motley::Checker object.

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
        $this->chkE = new Checker(E_USER_ERROR);
    }

    /// Set the arrangement name.
    /// @param $name - the arrangement name.
    public function setName(string $name) {
        $this->name = $name;
    }

    /// Get the arrangement name.
    /// @return the arrangement name.
    public function getName() : string {
        return $this->name;
    }
    
    /// Set the arrangement description.
    /// @param $desc - the arrangement description.
    public function setDescription(string $desc) {
        $this->description = $desc;
    }

    /// Get the arrangement description.
    /// @return the arrangement description.
    public function getDescription() : string {
        return $this->description;
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
            $name = $this->name;
        }
        return $name;
    }

    /// Add a component to the arrangement.
    /// @param $obj - CommandArg, CommandOpt,CommandOptGrp, or CommandDoubleDash.
    /// @param $optional - True for optional.
    /// @param $repeatable - True for repeatable.
    public function addComponent(CommandComponent $obj,
        bool $optional=false, bool $repeatable=false) {
        $arranComp = new CommandArrangeComp($obj,$optional,$repeatable);
        $this->arranComps[] = $arranComp;
    }

    /// Get the arrangement component array.
    /// @returns The arrangement component array of option groups, options, and args.
    public function getArranComps() : array {
        $result = $this->arranComps;
        return $result;
    }

    /// Clear the arrangement component array.
    public function clearArranComps() {
        $this->arranComps = array();
    }

    /// Try parsing command lines arguments.
    /// @param $argv - The command line argument array.
    /// @return TRUE if command line arguments match arrangement, else false.
    public function parse(array $argv) : bool {
        $this->argv = $argv;
        $ddFound = false;  // has "--" been found yet?
        $this->rewindParsing();
        $params = array_slice($argv,1);
        $aComps = $this->getArranComps();
        $param = array_shift($params);
        $aComp = array_shift($aComps);
        $match = null;
        $reason = "";
        while(true) {   // note: break command used to stop looping
            if($param===null) {
                // No more command line args.
                while($aComp!==null) {
                    // Check that remaining comps are fulfilled (optional or satisfied).
                    if($aComp->getIsFulfilled()===true) {
                        # fulfilled, we can skip this comp
                        $aComp = array_shift($aComps);
                        continue;
                    } else {
                        # mismatch!
                        $match = false;
                        $reason = "Not all required components fulfilled.";
                        break 2;  // exit both while loops
                    }
                }
                // No more params or components.
                // Apparently, we have a match!
                $match = true;
                $reason = "All requirements met.";
                break;
            } elseif($param=="--") {
                // found a bare double dash
                // skip fulfilled comps until validating double dash comp found.
                $classNames = array('Motley\CommandDoubleDash');
                $status = $this->skipFulfilledUntilClass(
                    $aComp,$aComps,$classNames,$param);
                if($status===false) {
                    # could not find double dash component, mismatch!
                    $match = false;
                    $reason = "Could not find '--' component.";
                    break;
                }
                # $aComp is a validated double dash.
                $ddFound = true;
                $this->matchingComps[] = $aComp->getCompObj();
                $param = array_shift($params);  // next command line param
            } elseif ($param!="-" and substr($param,0,1)=="-" and $ddFound==false) {
                # long or short form switch
                if(substr($param,0,2)=="--") {
                    $longForm = true;   # --xxx long form
                    $param1 = $param;
                    $param2 = null;
                    $possible = null;
                } else {
                    $longForm = false;  # -x short form
                    // look ahead for possible switch arg
                    $possible = array_shift($params);
                    if(is_null($possible)) {
                        $param1 = $param;
                        $param2 = null;
                    } else {
                        if(CommandOpt::checkOptSwitch($possible)) {
                            # $possible is another switch, not a switch arg.
                            # undo look ahead.
                            array_unshift($params,$possible);
                            $possible = null;
                            $param1 = $param;
                            $param2 = null;
                        } else {
                            # possible is a possible switch argument
                            $param1 = $param;
                            $param2 = "$param $possible";
                        }
                    }
                }
                $paramsToTry = array();
                if(!is_null($param2)) {
                    $paramsToTry[] = $param2;
                }
                $paramsToTry[] = $param1;
                $classNames = array();
                $classNames[] = 'Motley\CommandOpt';
                $classNames[] = 'Motley\CommandOptGrp';
                $aCompsSave = $aComps;
                # skip fulfilled comps until validating opt or optgrp found
                foreach($paramsToTry as $tryParam) {
                    $aComps = $aCompsSave;
                    $lastParam = $tryParam;
                    $status = $this->skipFulfilledUntilClass(
                        $aComp,$aComps,$classNames,$tryParam);
                    if($status) {
                        break;
                    }
                }
                if($status===false) {
                    # could not find validating opt or optgrp, mismatch!
                    $match = false;
                    $reason = "Option '$param' not expected here.";
                    break;
                }
                # $aComp is a validated option or option group.
                if($lastParam===$param1) {
                    # we didn't need the option arg param, we should unshift it back.
                    if(!is_null($possible)) {
                        array_unshift($params,$possible);
                    }
                }
                $comp = $aComp->getCompObj();
                if(is_a($comp,'Motley\CommandOptGrp')) {
                    # $comp is a CommandOptGrp
                    # save the embedded option that validated.
                    $this->matchingComps[] = $comp->getLastValidOption();
                } else {
                    # $comp is a CommandOpt
                    $this->matchingComps[] = $comp;
                }
                $param = array_shift($params);  // next command line param
            } else {
                # argument
                $classNames = array('Motley\CommandArg');
                $status = $this->skipFulfilledUntilClass(
                    $aComp,$aComps,$classNames,$param);
                if($status===false) {
                    # could not find a validating arg, mismatch!
                    $match = false;
                    $reason = "Argument '$param' not expected here.";
                    break;
                }
                $comp = $aComp->getCompObj();
                if(!in_array($comp,$this->matchingComps)) {
                    $this->matchingComps[] = $comp;
                }
                $param = array_shift($params);  // next command line param
            }
        }
        $this->matched = $match;
        $this->matchingReason = $reason;
        return $this->matched;
    }

    /// Get an array of components that were used for the last arrangement match.
    /// Note that CommandOptGrp objects are not returned however, rather the
    /// underlying CommandOpt within the groups that actually matched.
    /// @return As array of components matched in the last arrangement match.
    public function getMatchingComponents() : array {
        return $this->matchingComps;
    }

    /// Get the reason for the last arrangement match.
    /// @return The reason for for the last arrangement match.
    public function getMatchingReason() {
        $reason = $this->matchingReason;
        if($this->matched) {
            $reason = "MATCH: $reason";
        } else {
            $reason = "MISMATCH: $reason";
        }
        return $reason;
    }

    /// Skip ok to skip components until a proper one found.
    /// @param &$aComp - The current CommandArrangeComp, passed by ref.
    /// @param &$aComps - All remaining CommandArrangeComp objects, passed by ref.
    /// @param $classNames - Array of CommandComponent child classes.
    /// @param &$param - Current command line parameter, passed by ref.
    /// @return True if a proper validating CommandArrangeComp found.
    private function skipFulfilledUntilClass(&$aComp,&$aComps,$classNames,&$param) {
        if (!is_array($classNames)) { $className = array($classNames); }
        $result = false;
        while($aComp!==null) {
            $comp = $aComp->getCompObj();
            foreach($classNames as $className) {
                $foundClassMatch = false;
                if(is_a($comp,$className)) {
                    # Found matching class name.
                    $foundClassMatch = true;
                    $isRep = $aComp->getIsRepeatable();
                    $isOpt = $aComp->getIsOptional();
                    $isFul = $aComp->getIsFulfilled();
                    $hasVals = $aComp->getHasValidParamHistory();
                    if(!$isOpt and !$isFul) {
                        # we have a non-optional unfilled component, param must validate.
                        $stat = $comp->validate($param);
                        if($stat===true) {
                            return true;   # validating match
                        } else {
                            return false;  # requirements not met
                        }
                    }
                    # not (non-optional and non-fulfilled)
                    # ... optional or fulfilled
                    if($isRep) {
                        # we have an optional repeater or a fulfilled repeater
                        # see if param validates
                        $stat = $comp->validate($param);
                        if($stat===true) {
                            return true;   # validating match
                        } else {
                            if($isFul) {
                                break;     # ok to skip because already fulfilled
                            } else {
                                // unfulfilled repeater
                                // impossible path (repeat cond), but included for safety
                                // @codeCoverageIgnoreStart
                                return false;  # requirements not met
                                // @codeCoverageIgnoreEnd
                            }
                        }
                    }
                    # not (non-optional and non-fulfilled)
                    # ... optional or fulfilled
                    # not repeatable
                    if($hasVals) {
                        # not repeatable, but already has values, skip it
                        break;
                    }
                    # not (non-optional and non-fulfilled)
                    # ... optional or fulfilled
                    # not repeatable
                    # has no values
                    $stat = $comp->validate($param);
                    if($stat===true) {
                        # we just found a validating match
                        return true;
                    } else {
                        if($isOpt) {
                            break;  # ok to skip optional non-repeater
                        } else {
                            // impossible path (repeat cond), but included for safety
                            // @codeCoverageIgnoreStart
                            return false;  # requirements not met, non-optional
                            // @codeCoverageIgnoreEnd
                        }
                    }
                    # we didn't find a good match or a good skip
                    return false;
                }
            }  // end of class name for loop
            # Nothing found yet.
            # If current comp is fulfilled, skip to the next one, else mismatch!
            if($aComp->getIsFulfilled()) {
                $aComp = array_shift($aComps);
            } else {
                // impossible path (repeat cond), but included for safety
                // @codeCoverageIgnoreStart
                return false;
                // @codeCoverageIgnoreEnd
            }
        } // end of CommandArrangeComp while loop.
        # Went through all remaining components, but nothing matched param.
        return false;
    }
    private function rewindParsing() {
        $this->matched        = false;
        $this->nextCompIdx    = 0;
        $this->nextArgvIdx    = 1;
        $this->matchingComps  = array();
        $this->matchingReason = "";
        foreach($this->arranComps as $aComp) {
            $comp = $aComp->getCompObj();
            $comp->resetValidParamHistory();
        }
    }

}
?>

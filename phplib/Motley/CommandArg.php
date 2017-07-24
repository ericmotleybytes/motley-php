<?php
/// Class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

use Motley\GuidGenerator;

/// Represent a command line argument not bound to an option switch.
class CommandArg {

    protected $instanceGuid     = null;     ///< Unique guid for object instance.
    protected $validLiterals    = array();  ///< Valid literal arg values.
    protected $validRegExs      = array();  ///< Valid regular expressions.
    protected $isFilename       = false;    ///< Arg is a filename
    protected $fileMustExist    = false;    ///< if file, must it exist.
    protected $fileMustNotExist = false;    ///< if file, must it not exist.
    protected $lastArgValue     = "";       ///< Last arg value validated.
    protected $allArgValues     = array();  ///< All validated arg values.
    protected $lastMessage      = "";       ///< Last validation message.
    protected $lastIsValid      = false;    ///< Was last arg value valid.
    protected $argName          = "";       ///< arg name.
    protected $argDescription   = "";       ///< arg description.
    protected $isOptional       = false;    ///< Is arg optional.
    protected $displayName      = "";       ///< Argument display name.

    /// Class instance constructor.
    public function __construct(string $name=null, string $desc=null) {
        if(!is_null($name)) {
            $this->argName = $name;
        }
        if(!is_null($desc)) {
            $this->argDescription = $desc;
        }
        $guidGen = new GuidGenerator(false,false);
        $this->instanceGuid = $guidGen->generateGuid();
    }

    public function __clone() {
        // get a new instance guid for the cloned copy
        $guidGen = new GuidGenerator(false,false);
        $this->instanceGuid = $guidGen->generateGuid();
    }

    /// Get a newly instantiated instance copy. Safer and more flexible
    /// to use than the php built in 'clone' command.
    /// @param $name - new object argument name. If not specified or
    ///   if null, the arg name from the source object is unchanged.
    /// @param $desc - new object argument description. If not specified or
    ///   if null, the arg description from the source object is unchanged.
    public function copy(string $name=null, string $desc=null) : CommandArg {
        $objCopy = clone $this;
        if(!is_null($name)) {
            $objCopy->setArgName($name);
        }
        if(!is_null($desc)) {
            $objCopy->setArgDescription($desc);
        }
        return $objCopy;
    }

    /// Set the arg name.
    /// @param $name - the new arg name.
    public function setArgName(string $name) {
        $this->argName = $name;
    }

    /// Get the arg name.
    /// @return the current arg name.
    public function getArgName() : string {
        return $this->argName;
    }
    
    /// Set the arg description.
    /// @param $desc - the new arg description.
    public function setArgDescription(string $desc) {
        $this->argDescription = $desc;
    }

    /// Get the arg description.
    /// @return the current arg description.
    public function getArgDescription() : string {
        return $this->argDescription;
    }

    /// Get the object instance GUID.
    /// @return The unique instance GUID.
    public function getInstanceGuid() : string {
        return $this->instanceGuid;
    }

    /// Add a single valid literals.
    /// If the literal value is already on the valid list, then
    /// it is ignored.
    /// @param $lit - A literal valid value.
    /// @returns New number of valid literal values defined.
    public function addValidLiteral(string $lit) : int {
        if(!in_array($lit,$this->validLiterals)) {
            $this->validLiterals[] = $lit;
        }
        return count($this->validLiterals);
    }

    /// Add an array of valid literals.
    /// If a literal value is already on the valid list, then
    /// it is ignored.
    /// @param $lits - An array of string values.
    /// @returns New number of valid literal values defined.
    public function addValidLiterals(array $lits) : int {
        foreach($lits as $lit) {
            if(!in_array($lit,$this->validLiterals)) {
                $this->validLiterals[] = $lit;
            }
        }
        return count($this->validLiterals);
    }

    /// Get the list of valid literal values.
    /// @return The list of valid literal values.
    public function getValidLiterals() : array {
        return $this->validLiterals;
    }

    /// Clear all valid literals.
    public function clearValidLiterals() {
        $this->validLiterals = array();
    }

    /// Check if a candidate regular expression is properly formed.
    /// @callgraph
    /// @callergraph
    /// @param $regEx - A candidate regular expression to check.
    /// @return True if a proper regular expression, else false.
    public function checkRegEx(string $regEx) : bool {
        $saveErrorReporting = error_reporting(0); # turn off error reporting
        $check = preg_match($regEx,"dummy");
        error_reporting($saveErrorReporting); # turn error reporting back on
        if($check===false) {
            $result = false;
            $err = error_get_last;
            $msg = $err['message'];
            trigger_error($msg,E_USER_NOTICE);
        } else {
            $result = true;
        }
        return $result;
    }

    /// Add a single valid regular expressions.
    /// If the regular expression is already on the valid list, then
    /// it is ignored.
    /// @param $regEx - a string regex representation.
    /// @return New number of valid regex values defined.
    public function addValidRegEx(string $regEx) : int {
        if(!in_array($regEx,$this->validRegExs)) {
            if ($this->checkRegEx($regEx)) {
                $this->validRegExs[] = $regEx;
            }
        }
        return count($this->validRegExs);
    }

    /// Add an array of valid regular expressions.
    /// If a regular expression is already on the valid list, then
    /// it is ignored.
    /// @param $regExs - an array of string regexs.
    /// @return New number of valid regex values defined.
    public function addValidRegExs(array $regExs) : int {
        foreach($regExs as $regEx) {
            // add to array
           if(!in_array($regEx,$this->validRegExs)) {
                if ($this->checkRegEx($regEx)) {
                    $this->validRegExs[] = $regEx;
                }
            }
        }
        return count($this->validRegExs);
    }

    /// Get the list of valid regex values.
    /// @return The list of valid regular expression values.
    public function getValidRegExs() : array {
        return $this->validRegExs;
    }

    /// Clear all valid regexs.
    public function clearValidRegExs() {
        $this->validRegExs = array();
    }

    /// Set/unset the requirement that the argument specify an existent file.
    /// If set to true, the requirement that a file not exist is set to false.
    /// @param $flag - true if argument is a file specification, else false.
    ///   If false, the $exist parameter is ignored even if specified.
    /// @param $exist - if not specified it means file existence does not mater,
    ///   true means file must exist, false means file must not exist.
    public function setIsFile(bool $flag, bool $exist=null) {
        if ($flag) {
            $this->isFilename = true;
            if(is_null($exist)) {
                $this->fileMustExist    = false;
                $this->fileMustNotExist = false;
            } elseif ($exist===true) {
                $this->fileMustExist    = true;
                $this->fileMustNotExist = false;
            } else {
                $this->fileMustExist    = false;
                $this->fileMustNotExist = true;
            }
        } else {
            $this->isFilename       = false;
            $this->fileMustExist    = false;
            $this->fileMustNotExist = false;
        }
    }

    /// Get the true/false setting of the requirement that the argument
    /// be a file specification.
    /// @return The true/false setting of the requirement that the argument
    ///   be a file specification.
    public function getIsFile() : bool {
        return $this->isFilename;
    }

    /// Get the true/false setting of the file must exist flag.
    /// @return true if file must exist, else false.
    public function getFileMustExist() : bool {
        return $this->fileMustExist;
    }

    /// Get the true/false setting of the file must not exist flag.
    /// @return true if file must not exist, else false.
    public function getFileMustNotExist() : bool {
        return $this->fileMustNotExist;
    }

    /// Get the value of the last argument validated.
    /// @return The value of the last argument validated.
    public function getLastArgValue() : string {
        return $this->lastArgValue;
    }

    /// Get all successfully validated argument values.
    /// @return An array of successfully validated argument values.
    public function getAllArgValues() {
        return $this->allArgValues;
    }

    /// Clear all validated argument values.
    public function clearAllArgValues() {
        $this->allArgValues = array();
    }

    /// Get result of last validation.
    /// @return true if the last validation was ok, else false.
    public function getLastIsValid() : bool {
        return $this->lastIsValid;
    }

    /// Get the last validation message. Explains why the
    /// validation was ok or not.
    /// @return true if the last validation was ok, else false.
    public function getLastMessage() : string {
        return $this->lastMessage;
    }

    /// Test if an actual argument value meets the validation criteria
    /// of this command line argument.
    /// Also saves the value of the last argument validated and the
    /// validation result.
    /// @param $argument - A string value, typically a specific token
    ///   from the command line.
    /// @return true if the argument is valid, else false.
    public function validate(string $argument) : bool {
        $this->lastArgValue = $argument;
        $this->lastMessage  = "";
        $result = null;
        if(is_null($result)) {
            foreach($this->validLiterals as $literal) {
                if ($argument==$literal) {
                    $result = true;
                    $this->lastMessage = "valid (matches valid literal).";
                    break;
                }
            }
        }
        if (is_null($result)) {
            foreach($this->validRegExs as $regEx) {
                $check = preg_match($regEx,$argument);
                if ($check===false) {
                    // @codeCoverageIgnoreStart
                    // Impossible path, but included for defensive programming.
                    continue;
                    // @codeCoverageIgnoreEnd
                } elseif ($check==1) {
                    $result = true;
                    $this->lastMessage = "valid (matches valid regex).";
                    break;
                }
            }
        }
        if (is_null($result)) {
            if(count($this->getValidLiterals())==0) {
                if(count($this->getValidRegExs())==0) {
                    $result = true;
                    $this->lastMessage = "valid (no special restrictions).";
                }
            }
        }
        if (is_null($result)) {
            $result = false;
            $this->lastMessage = "invalid (no matching literals or patterns).";
        }
        if($result===true) {
            if($this->isFilename===true) {
                $fileExists = file_exists($argument);
                if($this->fileMustExist==true) {
                    if ($fileExists!==true) {
                        $result = false;
                        $this->lastMessage = "invalid (file does not exist).";
                    }
                }
                if($this->fileMustNotExist==true) {
                    if ($fileExists===true) {
                        $result = false;
                        $this->lastMessage = "invalid (file already exists).";
                    }
                }
            }
        }
        $this->lastIsValid = $result;
        if ($result===true) {
            $this->allArgValues[] = $argument;
        }
        return $result;
    }

    /// Set if argument is optional.
    /// @param $flag - True for argument is optional, else false.
    public function setIsOptional(bool $flag) {
        $this->isOptional = $flag;
    }

    /// Get if argument is optional.
    /// @return True if argument is optional, else false.
    public function getIsOptional() : bool {
        return $this->isOptional;
    }

    /// Set the argument display name for syntax help and so forth.
    /// @param $name - The argument display name.
    public function setDisplayName(string $name) {
        $this->displayName = $name;
    }

    /// Get the argument display name for syntax help and so forth.
    /// @returns The previously set display name, or the general name
    ///   if display name has not been explicitly set.
    public function getDisplayName() : string {
        $name = $this->displayName;
        if ($name == "") {
            $name = $this->argName;
        }
        return $name;
    }
}
?>

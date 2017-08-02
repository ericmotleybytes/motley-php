<?php
/// Source code file for the Motley::CommandArg class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\CommandComponent;

/// Represent a command line argument.
class CommandArg extends CommandComponent {

    protected $validLiterals    = array();  ///< Valid literal arg values.
    protected $validLitDescs    = array();  ///< Hash array of descriptions for literals.
    protected $validRegExs      = array();  ///< Valid regular expressions.
    protected $validRxDescs     = array();  ///< Hash array of reg ex descriptions.
    protected $isFilename       = false;    ///< Arg is a filename
    protected $fileMustExist    = false;    ///< if file, must it exist.
    protected $fileMustNotExist = false;    ///< if file, must it not exist.
    protected $defaultValue     = "";       ///< Argument default value.

    /// Class instance constructor.
    /// @param $name - The object name.
    /// @param $desc - The object description.
    public function __construct(string $name=null, string $desc=null) {
        parent::__construct($name,$desc);
    }

    /// Add a single valid literals.
    /// If the literal value is already on the valid list, then
    /// it only the description is updated.
    /// @param $lit - A literal valid value.
    /// @param $desc - A description associated with this literal.
    /// @returns New number of valid literal values defined.
    public function addValidLiteral(string $lit, string $desc="") : int {
        if(!in_array($lit,$this->validLiterals)) {
            # only add if not yet added
            $this->validLiterals[] = $lit;
        }
        $this->validLitDescs[$lit] = $desc;
        return count($this->validLiterals);
    }

    /// Get the list of valid literal values.
    /// @return The list of valid literal values.
    public function getValidLiterals() : array {
        return $this->validLiterals;
    }

    /// Get associative array mapping literal value to literal description.
    /// @return An associative array of literal value to description.
    public function getValidLitDescs() : array {
        return $this->validLitDescs;
    }

    /// Clear all valid literals.
    public function clearValidLiterals() {
        $this->validLiterals = array();
        $this->validLitDescs = array();
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
    /// the description is updated.
    /// @param $regEx - a string regex representation.
    /// @param $desc - A dedcription of the regular expression.
    /// @return New number of valid regex values defined.
    public function addValidRegEx(string $regEx, string $desc="") : int {
        if(!in_array($regEx,$this->validRegExs)) {
            if ($this->checkRegEx($regEx)) {
                $this->validRegExs[] = $regEx;
            }
            $this->validRxDescs[$regEx] = $desc;
        }
        return count($this->validRegExs);
    }

    /// Get the list of valid regex values.
    /// @return The list of valid regular expression values.
    public function getValidRegExs() : array {
        return $this->validRegExs;
    }

    /// Get associative array mapping regular expressions descriptions.
    /// @return An associative array of regular expression to description.
    public function getValidRxDescs() : array {
        return $this->validRxDescs;
    }

    /// Clear all valid regexs.
    public function clearValidRegExs() {
        $this->validRegExs  = array();
        $this->validRxDescs = array();
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

    /// Test if an actual argument value meets the validation criteria
    /// of this command line argument.
    /// Also saves the value of the last argument validated and the
    /// validation result.
    /// @param $param - A string parameter, typically a specific token
    /// from the command line.
    /// @return TRUE if $param is valid, else FALSE.
    public function validate(string $param) : bool {
        if($param=="-") {
            # replace "-" with default value
            $param = $this->defaultValue;
        }
        $result = null;
        // try to validate with literals
        foreach($this->validLiterals as $literal) {
            if ($param==$literal) {
                $result = true;
                $message = "'$param' matches valid literal.";
                break;
            }
        }
        if (is_null($result)) {
            // try to validate with regular expressions
            foreach($this->validRegExs as $regEx) {
                $check = preg_match($regEx,$param);
                if ($check===false) {
                    // @codeCoverageIgnoreStart
                    // Impossible path, but included for defensive programming.
                    continue;
                    // @codeCoverageIgnoreEnd
                } elseif ($check==1) {
                    $result = true;
                    $message = "'$param' matches valid regex.";
                    break;
                }
            }
        }
        if (is_null($result)) {
            // check if no literals or regexs required.
            if(count($this->getValidLiterals())==0) {
                if(count($this->getValidRegExs())==0) {
                    $result = true;
                    $message = "'$param' is ok (no special restrictions).";
                }
            }
        }
        if (is_null($result)) {
            // there were literals or regexs, but none matched
            $result = false;
            $message = "'$param' did not match any literals or patterns.";
        }
        if($result===true) {
            if($this->isFilename===true) {
                $fileExists = file_exists($param);
                if($this->fileMustExist==true) {
                    if ($fileExists!==true) {
                        $result = false;
                        $message = "'$param' does not exist.";
                    }
                }
                if($this->fileMustNotExist==true) {
                    if ($fileExists===true) {
                        $result = false;
                        $message = "'$param' already exists.";
                    }
                }
            }
        }
        $this->saveLastParam($param, $result, $message);
        return $result;
    }

    /// Get the argument display name for syntax help and so forth.
    /// @returns The previously set display name, or the general name
    /// if display name has not been explicitly set. If the display name is
    /// formed from the general name, the general name is enclosed
    /// within '<' and '>' and any spaces are replaced with '_' underscores.
    public function getDisplayName() : string {
        if(is_null($this->displayName) or $this->displayName=="") {
            $dispName = '<' . $this->getName() . '>';
            $dispName = str_replace(" ","_",$dispName);
        } else {
            $dispName = $this->displayName;
        }
        return $dispName;
    }

    /// Set the argument default value.
    /// @param $defVal - The argument default value.
    public function setDefaultValue(string $defVal) {
        $this->defaultValue = $defVal;
    }

    /// Get the argument default value.
    /// @return The argument default value.
    public function getDefaultValue() : string {
        return $this->defaultValue;
    }

}
?>

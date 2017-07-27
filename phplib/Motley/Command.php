<?php
/// Class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

use Motley\CommandArg;
use Motley\CommandArrange;

/// Represent a command line option.
class Command {

    const PLAIN_STYLE = "plain"; ///< Style for plain text help information.

    protected $cmdName          = "";       ///< Command name.
    protected $cmdDescription   = "";       ///< Command description.
    protected $cmdArrangements  = array();  ///< Command opt/arg layouts.
    protected $displayName      = "";       ///< Command display name.

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
    public function addArragement(CommandArrange $arrange) {
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

    /// Get help information.
    /// @param $style - Style of help display information. Legal values
    ///   include Command::PLAIN_STYLE ("plain').
    public function getHelp(string $style=self::PLAIN_STYLE) : string {
        if ($style==self::PLAIN_STYLE) {
            $result = "Usage:" . PHP_EOL;
            foreach($this->arrangements as $arrangement) {
                # TBD
            }
            
        }
        return $result;
    }

    /// Display help information to standard output.
    /// @param $style - Style of help display information.
    /// See Motley::Command::getHelp for legal values.
    public function displayHelp(string $style=self::PLAIN_STYLE) {
        echo($this->getHelpText);
    }
}
?>

<?php
/// Class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

use Motley\CommandArg;

/// Represent a command line option.
class Command {

    protected $cmdName          = "";       ///< Command name.
    protected $cmdDescription   = "";       ///< Command description.
    protected $cmdArrangements  = array();  ///< Command opt/arg layouts.

    /// Class instance constructor.
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
}
?>

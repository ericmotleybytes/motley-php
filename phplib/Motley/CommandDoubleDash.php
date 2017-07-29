<?php
/// Class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
///   MIT License. See <https://opensource.org/licenses/MIT>.
namespace Motley;

/// Represent a command line bare double-dash ('--').
/// This is traditionally used during command line parsing to indicate
/// that no more -x or -xxx options switches follow, only positional
/// arguments.
class CommandDoubleDash {

    protected $name        = "--";                  ///< Object name.
    protected $description = "A bare double-dash."; ///< Object description.

    /// Class instance constructor.
    public function __construct() {}

    /// Get the object name.
    /// @return the object name.
    public function getName() : string {
        return $this->name;
    }

    /// Get the object description.
    /// @return the object description.
    public function getDescription() : string {
        return $this->description;
    }

    /// Set the object description.
    /// @param $desc - the object description.
    public function setDescription(string $desc) {
        $this->description = $desc;;
    }

    /// Get the display name.
    /// @return the display name.
    public function getDisplayName() {
        return $this->name;
    }
}
?>

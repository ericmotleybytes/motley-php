<?php
/// Source code file for the Motley::CommandArrangeComp class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;

use Motley\CommandComponent;

/// Represent a command component within an arrangement.
class CommandArrangeComp {

    protected $compObj      = null;   ///< A Motley::CommandComponent instance.
    protected $isOptional   = false;  ///< Is component optional within arrangement.
    protected $isRepeatable = false;  ///< Is component repeatable within arrangement.

    /// Class instance constructor.
    /// @param $compObj - A CommandComponent object, or child.
    /// @param $isOptional - Is the component optional within the arrangement.
    /// @param $isRepeatable - Is the component repeatable within the arrangement.
    public function __construct(CommandComponent $compObj, bool $isOptional=false,
        bool $isRepeatable=false) {
        $this->compObj = $compObj;
        $this->isOptional = $isOptional;
        $this->isRepeatable = $isRepeatable;
    }

    /// Get the component object instance.
    /// @return The component object instance.
    public function getCompObj() : CommandComponent {
        return $this->compObj;
    }

    /// Get if component is optional in arrangement.
    /// @return TRUE if component is optional in arrangement.
    public function getIsOptional() : bool {
        return $this->isOptional;
    }

    /// Get if component is repeatable in arrangement.
    /// @return TRUE if component is repeatable in arrangement.
    public function getIsRepeatable() : bool {
        return $this->isRepeatable;
    }

    /// Get if this arrangement component has been fulfilled during parsing.
    /// A component is fulfilled if it is optional, or if it is mandatory but
    /// has at least one valid param in its history.
    /// @return TRUE if component parsing requirements at least minimally fulfilled.
    public function getIsFulfilled() : bool {
        $result = false;
        $comp = $this->compObj;
        if($this->isOptional) {
            $result = true;
        } elseif(count($comp->getValidParamHistory())>0) {
            $result = true;
        }
        return $result;
    }

    /// Get if underlying component already has validated values.
    /// @return TRUE if underlying component has some validated param history.
    public function getHasValidParamHistory() : bool {
        $result = false;
        $comp = $this->compObj;
        $cnt = count($comp->getValidParamHistory());
        if($cnt>0) {
            $result = true;
        }
        return $result;
    }
}
?>

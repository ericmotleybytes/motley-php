<?php
/// Source code file for Motley::Test::CommandComponentTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandComponent;

/// Tests the Motley::CommandComponent class.
class CommandComponentTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $comp = new CommandComponent("comp1","A component for unit testing.");
        $this->assertInstanceOf(CommandComponent::class,$comp);
    }

    /// Test setName/getName functions.
    public function testSetOptName() {
        $name1a = "comp1a";
        $name1b = "comp1b";
        $comp = new CommandComponent($name1a);
        $this->assertEquals($name1a,$comp->getName());
        $comp->setName($name1b);
        $this->assertEquals($name1b,$comp->getName());
    }

    /// Test setDescription/getDescription functions.
    public function testSetGetDescription() {
        $desc1a = "description 1a.";
        $desc1b = "description 1b.";
        $comp = new CommandComponent("comp1",$desc1a);
        $this->assertEquals($desc1a,$comp->getDescription());
        $comp->setDescription($desc1b);
        $this->assertEquals($desc1b,$comp->getDescription());
    }

    /// Test validate and get last parameter functions.
    public function testValiateGetLastParam() {
        $comp = new CommandComponent("comp1","Component for unit testing.");
        $p1 = "thing";
        $comp->validate($p1);
        $this->assertEquals($p1,$comp->getLastParamValue());
        $this->assertFalse($comp->getLastParamIsValid());
        $this->assertGreaterThan(0,strlen($comp->getLastParamValue()));
    }
}
?>

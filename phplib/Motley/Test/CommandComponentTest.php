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
use Motley\UnitTestSupport;

/// Tests the Motley::CommandComponent class.
class CommandComponentTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $comp = new CommandComponent("comp1","A component for unit testing.");
        $this->assertInstanceOf(CommandComponent::class,$comp);
    }

    /// Test setName/getName functions.
    public function testSetGetName() {
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

    /// Test set/get display name functions.
    public function testSetGetDisplayName() {
        $name = "Normal Name";
        $displayName = "Display Name";
        $comp = new CommandComponent($name);
        $this->assertEquals($name,$comp->getDisplayName());
        $comp->setDisplayName($displayName);
        $this->assertEquals($displayName,$comp->getDisplayName());
    }

    /// Test save last param function.
    public function testSaveLastParam() {
        $comp = new CommandComponent("comp1","Component for unit testing.");
        $p1 = "thing";
        $p2 = array("aaa","bbb");
        $r  = true;
        $m  = "message";
        UnitTestSupport::invokeFunction(
            $comp,"saveLastParam",array($p1,$r,$m)); // call protected function
        $this->assertEquals($p1,$comp->getLastParamValue());
        $this->assertEquals($r,$comp->getLastParamIsValid());
        $this->assertStringEndsWith($m,$comp->getLastParamMessage());
        UnitTestSupport::invokeFunction(
            $comp,"saveLastParam",array($p2,$r,$m)); // call protected function
        $this->assertEquals($p2,$comp->getLastParamValue());
        $this->assertEquals($r,$comp->getLastParamIsValid());
        $this->assertStringEndsWith($m,$comp->getLastParamMessage());
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

    /// Test validate and parameter history functions.
    public function testParamHistory() {
        $comp = new CommandComponent("comp1","Component for unit testing.");
        $exp = array();
        $hist = $comp->getValidParamHistory();
        $this->assertEquals($exp,$hist);
        $p1 = "thing";
        $exp = array($p1);
        UnitTestSupport::invokeFunction(
            $comp,"saveLastParam",array($p1,true,"dummy")); // call protected function
        $hist = $comp->getValidParamHistory();
        $this->assertEquals($exp,$hist);
        $p2 = "bad";
        UnitTestSupport::invokeFunction(
            $comp,"saveLastParam",array($p2,false,"dummy")); // call protected function
        $hist = $comp->getValidParamHistory();
        $this->assertEquals($exp,$hist);
        $p3 = "stuff";
        $exp[] = $p3;
        UnitTestSupport::invokeFunction(
            $comp,"saveLastParam",array($p3,true,"dummy")); // call protected function
        $hist = $comp->getValidParamHistory();
        $this->assertEquals($exp,$hist);
        # clear history
        $comp->resetValidParamHistory();
        $exp = array();
        $hist = $comp->getValidParamHistory();
        $this->assertEquals($exp,$hist);
    }

    /// Test callback functionality.
    public function testCallbacks() {
        $comp = new CommandComponent("testcomp");
        $callable = array($this,"sampleCallback");
        $stat = $comp->registerCallback($callable,"phase1");
        $this->assertTrue($stat);
        $this->assertEquals($callable,$comp->getCallback("phase1"));
        $stat = $comp->registerCallback($callable,"phase2");
        $this->assertTrue($stat);
        $this->assertEquals($callable,$comp->getCallback("phase2"));
        $this->assertFalse($comp->getCallback("phase9"));
        $exp = array("phase1","phase2");
        $act = $comp->getCallbackPhases();
        $this->assertEquals($exp,$act);
        $result = $comp->invokeCallback("phase1");
        $this->assertEquals("phase1",$result["phase"]);
        $this->assertEquals(get_class($comp),$result["classname"]);
        $this->assertEquals("",$result["more"]);
        $result = $comp->invokeCallback("phase2",array("more"=>"stuff"));
        $this->assertEquals("phase2",$result["phase"]);
        $this->assertEquals(get_class($comp),$result["classname"]);
        $this->assertEquals("stuff",$result["more"]);
        # unregister
        $this->assertTrue($comp->unregisterCallback("phase1"));
        $this->assertTrue($comp->unregisterCallback("phase2"));
        $this->assertTrue($comp->unregisterCallback("phase9"));
        $this->assertNull($comp->invokeCallback("phase1"));
        # try to register a non-callable
        // turn on error capturing
        UnitTestSupport::engageCaptureHandler(E_USER_WARNING);
        $badCallable = array("not","a","callable");
        $stat = $comp->registerCallback($badCallable);
        $this->assertFalse($stat);
        $this->assertEquals(1,count(UnitTestSupport::getCapturedErrors()));
        UnitTestSupport::clearCapturedErrors();
        // turn off error capturing
        UnitTestSupport::disengageCaptureHandler();
    }

    /// Define a callback function to be used by testCallbacks.
    /// @param $phase - Associated phase name.
    /// @param $xtraData - Extra data hash array.
    /// @returns An associative array with named result fields.
    public function sampleCallback(string $phase, $compObj, $xtraData) {
        $result = array();
        $result["more"] = "";
        if(array_key_exists("more",$xtraData)) {
            $result["more"] = $xtraData["more"];
        }
        $result["phase"] = $phase;
        $result["classname"] = get_class($compObj);
        return $result;
    }

    /// Test static function findComponentByName.
    public function testFindComponentByName() {
        $comp1  = new CommandComponent("comp1");
        $comp2a = new CommandComponent("comp2");
        $comp2b = new CommandComponent("comp2");
        $comp3  = new CommandComponent("comp3");
        $comps = array($comp1,$comp2a,$comp2b,$comp3);
        $exp = array($comp1);
        $this->assertEquals($exp,CommandComponent::findComponentByName("comp1",$comps));
        $exp = array($comp2a,$comp2b);
        $this->assertEquals($exp,CommandComponent::findComponentByName("comp2",$comps));
        $exp = array($comp3);
        $this->assertEquals($exp,CommandComponent::findComponentByName("comp3",$comps));
    }
}
?>

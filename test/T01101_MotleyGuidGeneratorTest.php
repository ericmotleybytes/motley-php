<?php
require_once(__DIR__.'/../phplib/Motley/GuidGenerator.php');

use PHPUnit\Framework\Testcase;
use Motley\GuidGenerator;

class T01101_MotleyGuidGeneratorTest extends Testcase {
    public function testNewGuidGenerator() {
        $guidGen1 = new GuidGenerator();
        $guidGen2 = new GuidGenerator();
        $this->assertFalse($guidGen1===$guidGen2);
    }
    public function testGlobalGuidGenerator() {
        $guidGen1 = GuidGenerator::getGlobalGuidGenerator();
        $guidGen2 = GuidGenerator::getGlobalGuidGenerator();
        $this->assertTrue($guidGen1===$guidGen2);
    }
    public function testLowerNoDashes() {
        $guidGen = new GuidGenerator();
        $this->assertFalse($guidGen->getUpperFlag());
        $this->assertFalse($guidGen->getDashesFlag());
        $guidGen->setUpperFlag(TRUE);
        $guidGen->setDashesFlag(TRUE);
        $this->assertTrue($guidGen->getUpperFlag());
        $this->assertTrue($guidGen->getDashesFlag());
        $guidGen->setUpperFlag(FALSE);
        $guidGen->setDashesFlag(FALSE);
        $this->assertFalse($guidGen->getUpperFlag());
        $this->assertFalse($guidGen->getDashesFlag());
        $guid = $guidGen->generateGuid();
        $this->assertRegExp('/^[0-9a-f]{32}$/',"$guid");
    }
    public function testUpperNoDashes() {
        $guidGen = new GuidGenerator(TRUE);
        $this->assertTrue($guidGen->getUpperFlag());
        $this->assertFalse($guidGen->getDashesFlag());
        $guid = $guidGen->generateGuid();
    }
}
?>

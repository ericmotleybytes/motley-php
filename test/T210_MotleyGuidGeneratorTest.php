<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;
use Motley\GuidGenerator;

/**
 * Tests the Motley::GuidGenerator class.
 */
class T210_MotleyGuidGeneratorTest extends Testcase {

    /**
     * Test instantiating separate instances.
     */
    public function testNewGuidGenerator() {
        $guidGen1 = new GuidGenerator();
        $guidGen2 = new GuidGenerator();
        $this->assertFalse($guidGen1===$guidGen2);
        $guidGen3 = new GuidGenerator(null,null);
        $guidGen4 = new GuidGenerator(true,true);
    }

    /// Test getting the common global instance.
    public function testGlobalGuidGenerator() {
        $guidGen1 = GuidGenerator::getGlobalGuidGenerator();
        $guidGen2 = GuidGenerator::getGlobalGuidGenerator();
        $this->assertTrue($guidGen1===$guidGen2);
    }

    /// Test generating a guid in lowercase with no dashes.
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

    /// Test generating a guid in uppercase with no dashes.
    public function testUpperNoDashes() {
        $this->assertNotNull($guidGen = new GuidGenerator(TRUE));
        $this->assertTrue($guidGen->getUpperFlag());
        $this->assertFalse($guidGen->getDashesFlag());
        $guid = $guidGen->generateGuid();
    }

}
?>

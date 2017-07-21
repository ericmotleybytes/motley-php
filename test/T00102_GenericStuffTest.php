<?php
use PHPUnit\Framework\Testcase;

class T00102_GenericStuffTest extends Testcase {
    public function testHashArray() {
        $hash = [];
        $this->assertEquals(0, count($hash));
        $hash["apple"] = "fruit";
        $hash["dog"]   = "animal";
        $this->assertEquals(2, count($hash));
        $this->assertEquals('fruit',  $hash["apple"]);
        $this->assertEquals('animal', $hash["dog"]);
    }
    public function testDouglasAdams() {
        $stack = [];
        $this->assertEquals(42, 2*3*7);
    }
}
?>

<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;
use Motley\UsageFormatter;

/// Tests the Motley::UsageFormatter class.
class T210_MotleyUsageFormatterTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $fmt1 = new UsageFormatter();
        $this->assertInstanceOf(UsageFormatter::class,$fmt1);
    }

    /// test clear and output.
    public function testClearOutput() {
        $fmt = new UsageFormatter();
        $s1a = "abcdef";
        $fmt->formatChunk($s1a);
        $s1b = rtrim($fmt->getFormattedText());
        $this->assertEquals($s1a,$s1b);
        ob_start();  # start stdout capture
        $fmt->outputFormattedText();
        $s1c = rtrim(ob_get_contents());
        ob_end_clean();  # turn off stdout capture
        $this->assertEquals($s1a,$s1c);
        $fmt->clear();
        $s2 = rtrim($fmt->getFormattedText());
        $this->assertEquals("",$s2);
    }

    /// test set/get column width.
    public function testSetGetColumnWidth() {
        $fmt = new UsageFormatter();
        $oldColEnv = getenv("COLUMNS");
        putenv("COLUMNS");  # unset env var
        $this->assertGreaterThan(0,$fmt->getColumnWidth());
        $w1 = 70;
        putenv("COLUMNS=$w1");
        $this->assertEquals($w1,$fmt->getColumnWidth());
        $w2 = 75;
        $fmt->setColumnWidth($w2);
        $this->assertEquals($w2,$fmt->getColumnWidth());
        if ($oldColEnv===false) {
            putenv("COLUMNS");  # unset env var
        } else {
            setenv("COLUMNS=$oldColEnv");
        }
    }

    /// test set/get left indent.
    public function testSetGetLeftIndent() {
        $fmt = new UsageFormatter();
        $this->assertEquals(0,$fmt->getLeftIndent());
        $ind = 2;
        $fmt->setLeftIndent($ind);
        $this->assertEquals($ind,$fmt->getLeftIndent());
    }

    /// test set/get right indent.
    public function testSetGetRightIndent() {
        $fmt = new UsageFormatter();
        $this->assertEquals(0,$fmt->getRightIndent());
        $ind = 2;
        $fmt->setRightIndent($ind);
        $this->assertEquals($ind,$fmt->getRightIndent());
    }

    /// test set/get continue indent.
    public function testSetGetContIndent() {
        $fmt = new UsageFormatter();
        $this->assertEquals(0,$fmt->getContinueIndent());
        $ind = 2;
        $fmt->setContinueIndent($ind);
        $this->assertEquals($ind,$fmt->getContinueIndent());
    }

    /// test set/get max length.
    public function testSetGetMaxLength() {
        $fmt = new UsageFormatter();
        $one2nine = "123456789";
        $fmt->formatChunk("123456789");
        $this->assertEquals(0,$fmt->getMaxLength());
        $out = $fmt->getFormattedText();
        $this->assertEquals($one2nine,rtrim($out));
        $fmt->setMaxLength(6);
        $this->assertEquals(6,$fmt->getMaxLength());
        $out = $fmt->getFormattedText();
        $this->assertEquals(substr($one2nine,0,6),rtrim($out));
    }

    /// test set/get eol.
    public function testSetGetEOL() {
        $fmt = new UsageFormatter();
        $this->assertEquals(PHP_EOL,$fmt->getEOL());
        $fmt->setEOL("bork");
        $this->assertEquals("bork",$fmt->getEOL());
    }

    /// Call a protected or private function in a class under test.
    /// This is a unit test helper function.
    /// @param $obj - Instantiated class object.
    /// @param $funcname - The name of the function to call.
    /// @param $argArr - Array of function arguments.
    /// @return Whatever the called function returns.
    protected function invokeFunction(&$obj, $funcName, array $argArr=array()) {
        $reflect  = new \ReflectionClass(get_class($obj));
        $function = $reflect->getMethod($funcName);
        $function->setAccessible(true);
        $result = $function->invokeArgs($obj,$argArr);
        return $result;
    }

    /// Test get/get param.
    public function testGetSetParam() {
        $fmt = new UsageFormatter();
        $this->invokeFunction(
            $fmt,"setParam",array(UsageFormatter::LEFT_INDENT,4));
        $leftIndent = $this->invokeFunction(
            $fmt,"getParam",array(UsageFormatter::LEFT_INDENT));
        $this->assertEquals(4,$leftIndent);
        $params = $this->invokeFunction(
            $fmt,"getParams",array());
        $leftIndent = $params[UsageFormatter::LEFT_INDENT];
        $this->assertEquals(4,$leftIndent);
    }

}
?>

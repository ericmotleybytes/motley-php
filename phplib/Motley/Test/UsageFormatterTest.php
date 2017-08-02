<?php
/// Source code file for Motley::Test::UsageFormatterTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\UsageFormatter;
use Motley\UnitTestSupport;

/// Tests the Motley::UsageFormatter class.
class UsageFormatterTest extends Testcase {

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

    /// Test formatting.
    public function testFormatting() {
        $expectedText =
            '[BEGIN TEST FORMATTING]' . PHP_EOL .
            'Hello world!' . PHP_EOL .
            '  This is a very long and laborious' . PHP_EOL .
            '   sentence. It goes on and on and' . PHP_EOL .
            '   on. This exposition of ridiculous' . PHP_EOL .
            '   prose is simply for testing and' . PHP_EOL .
            '   demonstration purposes only.' . PHP_EOL .
            'Options:' . PHP_EOL .
            '  -h | --help   Display help.' . PHP_EOL .
            '                     More help.' . PHP_EOL .
            '  -v | --version' . PHP_EOL .
            '                Display version.' . PHP_EOL .
            '  -d | --debug  Display additional debug' . PHP_EOL .
            '                information to standard' . PHP_EOL .
            '                error.' . PHP_EOL .
            '      thingAt2' . PHP_EOL .
            '      thingAt4' . PHP_EOL .
            '      thingAt6' . PHP_EOL .
            '       thingAt8' . PHP_EOL .
            'abcdefghijklmnopqrst' . PHP_EOL .
            ' uvwxyz' . PHP_EOL .
            '         abcdefghijk' . PHP_EOL .
            '         lmnopqrstuv' . PHP_EOL .
            '         wxyz' . PHP_EOL .
            '[END TEST FORMATTING]';
        # init formatter
        $w = new UsageFormatter();
        $w->setContinueIndent(1);
        $w->setColumnWidth(40);
        $w->setLeftIndent(0);
        $w->setRightIndent(0);
        # mark beginning
        $w->formatChunk('[BEGIN TEST FORMATTING]');
        $w->formatBreak();
        # say hello
        $w->formatText("Hello world!");
        $w->formatBreak();
        # format a long sentence
        $w->setLeftIndent(2);
        $w->setRightIndent(4);
        $t =  "This is a very long and laborious sentence. It goes ";
        $t .= "on and on and on. This exposition of ridiculous prose ";
        $t .= "is simply for testing and demonstration purposes only.";
        $w->formatText($t);
        $w->formatBreak();
        # format some more help like stuff
        $w->setLeftIndent(0);
        $w->setRightIndent(0);
        # line
        $c=17;
        $w->formatChunk("Options:");
        $w->formatBreak();
        # line
        $w->setLeftIndent(2);
        $w->formatChunk("-h | --help");
        $w->formatText("Display help.",$c);
        $w->formatText("More help.",$c+5);
        $w->formatBreak();
        # line
        $w->formatChunk("-v | --version");
        $w->formatText("Display version.",$c);
        $w->formatBreak();
        # line
        $w->formatChunk("-d | --debug");
        $w->formatText("Display additional debug information to standard error.",$c);
        $w->formatBreak();
        # weird stuff
        $w->setLeftIndent(4);
        $w->setContinueIndent(2);
        $w->setRightIndent(0);
        $w->formatChunk("thingAt2",2);
        $w->formatChunk("thingAt4",4);
        $w->formatChunk("thingAt6",6);
        $w->formatChunk("thingAt8",8);
        $w->formatBreak();
        # force a cut at no specific column
        $w->setLeftIndent(0);
        $w->setContinueIndent(1);
        $w->setRightIndent(20);
        $w->formatChunk("abcdefghijklmnopqrstuvwxyz");
        $w->formatBreak();
        # force a cut at a specific column
        $w->formatChunk("abcdefghijklmnopqrstuvwxyz",10);
        $w->formatBreak();
        # mark end
        $w->setLeftIndent(0);
        $w->setRightIndent(0);
        $w->setContinueIndent(0);
        $w->formatChunk('[END TEST FORMATTING]');
        # test results
        $actualText = $w->getFormattedText();
        $this->assertEquals($expectedText,$actualText);
    }

    /// test set/get column width.
    public function testSetGetColumnWidth() {
        $fmt = new UsageFormatter();
        $this->assertGreaterThan(0,$fmt->getColumnWidth());
        $w2 = 75;
        $fmt->setColumnWidth($w2);
        $this->assertEquals($w2,$fmt->getColumnWidth());
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

    /// Test get/get param.
    public function testGetSetParam() {
        $fmt = new UsageFormatter();
        UnitTestSupport::invokeFunction(
            $fmt,"setParam",array(UsageFormatter::LEFT_INDENT,4));
        $leftIndent = UnitTestSupport::invokeFunction(
            $fmt,"getParam",array(UsageFormatter::LEFT_INDENT));
        $this->assertEquals(4,$leftIndent);
        $params = UnitTestSupport::invokeFunction(
            $fmt,"getParams",array());
        $leftIndent = $params[UsageFormatter::LEFT_INDENT];
        $this->assertEquals(4,$leftIndent);
        # try setting a bad parameter name
        UnitTestSupport::engageCaptureHandler(E_USER_WARNING);
        UnitTestSupport::invokeFunction(
            $fmt,"setParam",array("BadParamName",99));
        UnitTestSupport::disengageCaptureHandler();
        $errs = UnitTestSupport::getCapturedErrors();
        $this->assertEquals(1,count($errs));
    }

}
?>

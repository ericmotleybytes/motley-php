<?php
/// Source code file for Motley::Test::CommandArgTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandArg;

/// Tests the Motley::CommandArg class.
class CommandArgTest extends Testcase {

    /// Test instantiation.
    public function testNew() {
        $arg1 = new CommandArg();
        $this->assertInstanceOf(CommandArg::class,$arg1);
        $arg2 = new CommandArg("arg2");
        $this->assertInstanceOf(CommandArg::class,$arg2);
        $arg3 = new CommandArg("arg3","A test instance.");
        $this->assertInstanceOf(CommandArg::class,$arg3);
    }

    /// Test overridden getDisplayName function.
    public function testGetDisplayName() {
        $arg1name = "arg1";
        $arg1 = new CommandArg($arg1name);
        $exp1 = '<' . $arg1name . '>';
        $this->assertEquals($exp1,$arg1->getDisplayName());
        $dispName = '<MyArgument>';
        $arg1->setDisplayName($dispName);
        $this->assertEquals($dispName,$arg1->getDisplayName());
    }

    /// Test valid literals and validate functions.
    public function testValidLiterals() {
        $arg1 = new CommandArg("arg1","arg1 description.");
        $lit1 = "aaa";
        $lit2 = "bbb";
        $lit3 = "ccc";
        $lit1desc = "aaa description.";
        $lit2desc = "bbb description.";
        $lit3desc = "ccc description.";
        $invalid1 = "bork";
        $invalid2 = "";
        $arr1 = array($lit1);
        $arr2 = array($lit2,$lit3);
        $arr3 = array_merge($arr1,$arr2);
        $this->assertEquals(0,count($arg1->getValidLiterals()));
        $arg1->addValidLiteral($lit1,$lit1desc);
        $this->assertEquals(1,count($arg1->getValidLiterals()));
        $this->assertEquals($arr1,$arg1->getValidLiterals());
        $arg1->addValidLiteral($lit1,$lit1desc);  // try adding dup (mostly ignored)
        $this->assertEquals(1,count($arg1->getValidLiterals()));
        $this->assertEquals($arr1,$arg1->getValidLiterals());
        $arg1->addValidLiteral($lit2,$lit2desc);
        $arg1->addValidLiteral($lit3,$lit3desc);
        $this->assertEquals($arr3,$arg1->getValidLiterals());
        # get back some descriptions
        $descs = $arg1->getValidLitDescs();
        $this->assertEquals($lit1desc,$descs[$lit1]);
        $this->assertEquals($lit2desc,$descs[$lit2]);
        $this->assertEquals($lit3desc,$descs[$lit3]);
        # try some validations
        $this->assertTrue($arg1->validate($lit1));
        $this->assertTrue($arg1->validate($lit2));
        $this->assertTrue($arg1->validate($lit3));
        $this->assertFalse($arg1->validate($invalid1));
        $this->assertFalse($arg1->validate($invalid2));
        $arg1->clearValidLiterals();
        $this->assertEquals(array(),$arg1->getValidLiterals());
    }

    /// Test valid regular expressions and validate functions.
    public function testValidRegExs() {
        $arg1 = new CommandArg("arg1","arg1 description.");
        $pat1 = '/^[0-9]+$/';
        $pat2 = '/^[a-z]+$/';
        $pat3 = '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/';
        $pat1desc = "Numeric description.";
        $pat2desc = "Lower description.";
        $pat3desc = "Date description.";
        $valid1 = "42";
        $valid2 = "abc";
        $valid3 = "2017-07-23";
        $invalid1 = "abc2";
        $invalid2 = "aBc";
        $invalid3 = "";
        $arr1 = array($pat1);
        $arr2 = array($pat2,$pat3);
        $arr3 = array_merge($arr1,$arr2);
        $this->assertEquals(0,count($arg1->getValidRegExs()));
        $arg1->addValidRegEx($pat1,$pat1desc);
        $this->assertEquals(1,count($arg1->getValidRegExs()));
        $this->assertEquals($arr1,$arg1->getValidRegExs());
        $arg1->addValidRegEx($pat1,$pat1desc);  // try adding duplicate
        $this->assertEquals(1,count($arg1->getValidRegExs()));
        $this->assertEquals($arr1,$arg1->getValidRegExs());
        $arg1->addValidRegEx($pat2,$pat2desc);
        $arg1->addValidRegEx($pat3,$pat3desc);
        $this->assertEquals($arr3,$arg1->getValidRegExs());
        # check the descriptions
        $descs = $arg1->getValidRxDescs();
        $this->assertEquals($pat1desc,$descs[$pat1]);
        $this->assertEquals($pat2desc,$descs[$pat2]);
        $this->assertEquals($pat3desc,$descs[$pat3]);
        # try some validations
        $this->assertTrue($arg1->validate($valid1));
        $this->assertTrue($arg1->validate($valid2));
        $this->assertTrue($arg1->validate($valid3));
        $this->assertFalse($arg1->validate($invalid1));
        $this->assertFalse($arg1->validate($invalid2));
        $this->assertFalse($arg1->validate($invalid3));
        $arg1->clearValidRegExs();
        $this->assertEquals(array(),$arg1->getValidRegExs());
    }

    /// Test set/get isFile functions.
    public function testSetGetIsFile() {
        $arg1 = new CommandArg("arg1","arg1 description.");
        $this->assertFalse($arg1->getIsFile());
        $this->assertFalse($arg1->getFileMustExist());
        $this->assertFalse($arg1->getFileMustNotExist());
        $arg1->setIsFile(true);
        $this->assertTrue($arg1->getIsFile());
        $this->assertFalse($arg1->getFileMustExist());
        $this->assertFalse($arg1->getFileMustNotExist());
        $arg1->setIsFile(false);
        $this->assertFalse($arg1->getIsFile());
        $this->assertFalse($arg1->getFileMustExist());
        $this->assertFalse($arg1->getFileMustNotExist());
        $arg1->setIsFile(true,true);
        $this->assertTrue($arg1->getIsFile());
        $this->assertTrue($arg1->getFileMustExist());
        $this->assertFalse($arg1->getFileMustNotExist());
        $arg1->setIsFile(true,false);
        $this->assertTrue($arg1->getIsFile());
        $this->assertFalse($arg1->getFileMustExist());
        $this->assertTrue($arg1->getFileMustNotExist());
    }

    /// Test arg value and arg message functions.
    public function testArgValues() {
        $arg1 = new CommandArg("arg1","arg1 description.");
        $arg1->addValidRegEx('/^[0-9a-z]*$/');
        $expVals = array();
        $this->assertEquals("",$arg1->getLastParamValue());
        $this->assertEquals("",$arg1->getLastParamMessage());
        $this->assertFalse($arg1->getLastParamIsValid());
        # validate a valid value
        $val = "42";
        $this->assertTrue($arg1->validate($val));
        $this->assertTrue($arg1->getLastParamIsValid());
        $this->assertEquals($val,$arg1->getLastParamValue());
        $this->assertRegExp('/^VALID.*$/',$arg1->getLastParamMessage());
        # validate another valid value
        $val = "good";
        $this->assertTrue($arg1->validate($val));
        $this->assertTrue($arg1->getLastParamIsValid());
        $this->assertEquals($val,$arg1->getLastParamValue());
        $this->assertRegExp('/^VALID.*$/',$arg1->getLastParamMessage());
        # validate an invalid value
        $val = "BAD";
        $this->assertFalse($arg1->validate($val));
        $this->assertFalse($arg1->getLastParamIsValid());
        $this->assertEquals($val,$arg1->getLastParamValue());
        $this->assertRegExp('/^INVALID.*$/',$arg1->getLastParamMessage());
    }

    /// Test good checkRegEx.
    public function testGoodCheckRegEx() {
        $arg1 = new CommandArg("arg1","arg1 description.");
        $this->assertTrue($arg1->checkRegEx('/^[0-9]+$/'));
    }

    /// Test bad checkRegEx.
    public function testBadCheckRegEx() {
        $arg1 = new CommandArg("arg1","arg1 description.");
        $saveErrorReporting = error_reporting(0); # turn off error reporting
        $check = $arg1->checkRegEx('/^[0-9]+$');  # no '/' terminator
        error_reporting($saveErrorReporting); # turn error reporting back on
        $this->assertFalse($check);
    }

    /// Test more validate cases.
    public function testValidate() {
        $arg1 = new CommandArg("arg1","arg1 description.");
        $this->assertTrue($arg1->validate("anything"));
        $arg1->addValidLiteral("aaa");
        $arg1->addValidRegEx('/^bbb$/');
        $this->assertTrue($arg1->validate("aaa"));
        $this->assertTrue($arg1->validate("bbb"));
        $this->assertFalse($arg1->validate("ccc"));
        $arg1 = new CommandArg("arg1","arg1 description.");
        $arg1->addValidRegEx('/^bbb$/');
        $this->assertFalse($arg1->validate("ccc"));
        $arg1 = new CommandArg("arg1","arg1 description.");
        $existingFile = __FILE__;
        $nonexistingFile = "a-non-existing-file.bork";
        $arg1->setIsFile(true);
        $this->assertTrue($arg1->validate($existingFile));
        $this->assertTrue($arg1->validate($nonexistingFile));
        $arg1->setIsFile(true,true);
        $this->assertTrue($arg1->validate($existingFile));
        $this->assertFalse($arg1->validate($nonexistingFile));
        $arg1->setIsFile(true,false);
        $this->assertFalse($arg1->validate($existingFile));
        $this->assertTrue($arg1->validate($nonexistingFile));
    }

    /// Test set/get display name functions.
    public function testSetGetDisplayName() {
        $arg1Name = "Normal Name";
        $arg1DisplayName = "Display Name";
        $arg1 = new CommandArg($arg1Name);
        $exp = "<Normal_Name>";
        $this->assertEquals($exp,$arg1->getDisplayName());
        $arg1->setDisplayName($arg1DisplayName);
        $this->assertEquals($arg1DisplayName,$arg1->getDisplayName());
    }

    /// Test set/get default value
    public function testSetGetDefaultValue() {
        $arg = new CommandArg("arg");
        $this->assertEquals("",$arg->getDefaultValue());
        $defVal="bork";
        $arg->setDefaultValue($defVal);
        $this->assertEquals($defVal,$arg->getDefaultValue());
        $arg->validate("-"); // shorthand for default value
        $this->assertEquals($defVal,$arg->getLastParamValue());
    }

}
?>

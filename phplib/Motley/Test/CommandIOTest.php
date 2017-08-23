<?php
/// Source code file for Motley::Test::CommandIOTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

use PHPUnit\Framework\Testcase;
use Motley\CommandIO;
use Motley\UnitTestSupport;

/// Tests the Motley::CommandIO class.
class CommandIOTest extends Testcase {

    /// Test instantiation and basic property gets.
    public function testNew() {
        $fn = "php://memory";
        $md = "a+";
        $io = new CommandIO($fn,$md);
        $this->assertInstanceOf(CommandIO::class,$io);
        $this->assertEquals($fn,$io->getFilename());
        $this->assertEquals($md,$io->getMode());
        $fh = $io->getHandle();
        $this->assertEquals("stream",get_resource_type($fh));
        $io->__destruct();
    }

    public function testIO() {
        $fn = "php://memory";
        $md = "w+";
        $s1 = "Hello" . PHP_EOL;
        $io = new CommandIO($fn,$md);
        $bytes = $io->write($s1);
        $this->assertEquals(strlen($s1),$bytes);
        $this->assertTrue($io->flush());
        $this->assertTrue($io->rewind());
        $s2 = $io->gets();
        $this->assertEquals($s1,$s2);
        $io->reopen($fn,$md);  // clear and reset php://memory
        $s3 = "World!" . PHP_EOL;
        $bytes = $io->write($s3);
        $this->assertEquals(strlen($s3),$bytes);
        $this->assertTrue($io->rewind());
        $s4 = $io->gets();
        $this->assertEquals($s3,$s4);
        $io->close();
    }

    public function testIoChar() {
        $fn = "php://memory";
        $md = "w+";
        $io = new CommandIO($fn,$md);
        $this->assertEquals(1,$io->puts("t"));
        $this->assertEquals(1,$io->puts("e"));
        $this->assertEquals(1,$io->puts("s",1));
        $this->assertEquals(1,$io->puts("t",2));
        $this->assertTrue($io->rewind());
        $this->assertFalse($io->eof());
        $str = "";
        while(($c=$io->getc())!==false) {
            $str .= $c;
        }
        $this->assertEquals("test",$str);
        $this->assertTrue($io->rewind());
        $str = $io->read(4);
        $this->assertEquals("test",$str);
        $this->assertTrue($io->rewind());
        $str = $io->read(1000);
        $this->assertEquals("test",$str);
        $pos = $io->tell();
        $this->assertEquals(4,$pos);
        $this->assertTrue($io->rewind());
        $this->assertFalse($io->eof());
        $this->assertEquals(0,$io->seek(0,SEEK_END));
        $c = $io->getc();
        $this->assertFalse($c);
        $this->assertTrue($io->eof());
        $stat = $io->stat();
        $this->assertEquals(4,$stat["size"]);
        $this->assertTrue($io->truncate(2));
        $stat = $io->stat();
        $this->assertEquals(2,$stat["size"]);
        $io->close();
    }

    public function testLock() {
        $unt = new UnitTestSupport();
        $unt->setTmpDirRoot(sys_get_temp_dir());
        $subdir = $unt->createTmpSubDir();
        $tmpfile = "$subdir/bork.bork";
        $tmpfile2 = $unt->createTmpDummyFile($tmpfile);
        $this->assertFileExists($tmpfile);
        $md = "w+";
        $io = new CommandIO($tmpfile,$md);
        $this->assertTrue($io->lock(LOCK_SH));
        $this->assertTrue($io->lock(LOCK_UN));
        $wb = -1;
        $this->assertTrue($io->lock(LOCK_SH,$wb));
        $this->assertEquals(0,$wb);
        $this->assertTrue($io->lock(LOCK_UN,$wb));
        $this->assertEquals(0,$wb);
        $io->close();
        $unt->cleanupTmp();
        $this->assertFileNotExists($tmpfile);
    }

    public function testStrip() {
        $fn = "php://memory";
        $md = "w+";
        $io = new CommandIO($fn,$md);
        $s1a = "This is a <b>test!</b><br/>" . PHP_EOL;
        $s1b = "This is a test!" . PHP_EOL;
        $s1c = "This is a test!<br/>" . PHP_EOL;
        $bytes = $io->write($s1a);
        $this->assertTrue($io->rewind());
        $s2b = $io->getss();
        $this->assertEquals($s1b,$s2b);
        $this->assertTrue($io->rewind());
        $s2c = $io->getss(2000,"<br>");
        $this->assertEquals($s1c,$s2c);
        $io->close();
    }

    public function testCSV() {
        $fn = "php://memory";
        $md = "w+";
        $io = new CommandIO($fn,$md);
        $a1 = array("aaa","bbb",42);
        $a2 = array("ccc","ddd",0);
        $bytes = $io->putcsv($a1);
        $this->assertGreaterThan(5,$bytes);
        $bytes = $io->putcsv($a2);
        $this->assertGreaterThan(5,$bytes);
        $this->assertTrue($io->rewind());
        $a3 = $io->getcsv();
        $this->assertEquals($a1,$a3);
        $a4 = $io->getcsv();
        $this->assertEquals($a2,$a4);
        $io->close();
    }

    public function testScanf() {
        $fn = "php://memory";
        $md = "w+";
        $io = new CommandIO($fn,$md);
        $s1 = "aaa\tbbb\tccc\n";
        $a1 = array("aaa","bbb","ccc");
        $bytes = $io->write($s1);
        $this->assertTrue($io->rewind());
        $stuff = $io->scanf("%s\t%s\t%s\n");
        $this->assertEquals($a1,$stuff);
        $io->close();
    }

    public function testEchoAndPrint() {
        $fn = "php://memory";
        $md = "w+";
        $io = new CommandIO($fn,$md);
        $bytes = $io->echo("aaa","bbb","\n");
        $this->assertGreaterThan(5,$bytes);
        $code = $io->print("xyz\n");
        $this->assertEquals(1,$code);
        $this->assertTrue($io->rewind());
        $stuff = $io->gets();
        $this->assertEquals("aaabbb\n",$stuff);
        $stuff = $io->gets();
        $this->assertEquals("xyz\n",$stuff);
        $io->close();
    }

    public function testPrintR() {
        $fn = "php://memory";
        $md = "w+";
        $io = new CommandIO($fn,$md);
        $num = 42;
        $stat = $io->print_r($num);
        $this->assertTrue($stat);
        $this->assertTrue($io->rewind());
        $stuff = $io->read(1000);
        $this->assertEquals("42",$stuff);
        $io->close();
    }

    public function testPrintf() {
        $fn = "php://memory";
        $md = "w+";
        $io = new CommandIO($fn,$md);
        $num = 42;
        $bytes = $io->printf('%1$03d',$num);
        $this->assertEquals(3,$bytes);
        $this->assertTrue($io->rewind());
        $stuff = $io->read(1000);
        $this->assertEquals("042",$stuff);
        $io->close();
    }

    public function testVprintf() {
        $fn = "php://memory";
        $md = "w+";
        $io = new CommandIO($fn,$md);
        $num = 42;
        $bytes = $io->vprintf('%1$03d',array($num));
        $this->assertEquals(3,$bytes);
        $this->assertTrue($io->rewind());
        $stuff = $io->read(1000);
        $this->assertEquals("042",$stuff);
        $io->close();
    }

}
?>

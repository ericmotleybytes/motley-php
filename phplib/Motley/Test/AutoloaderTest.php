<?php
/// Source code file for Motley::Test::AutoloaderTest unit testing class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley\Test;

require_once(__DIR__.'/../Autoloader.php');  // be explicit

use PHPUnit\Framework\Testcase;

/// Tests the Motley::Autoloader class.
class AutoloaderTest extends Testcase {

    /// A unit test set-uo function to initialize some tests.
    protected function setUp() {
        \Motley\Autoloader::unregister();
    }

    /// A unit test tear-down function to possibly restore autoloaders.
    protected function tearDown() {
        \Motley\Autoloader::register();
    }

    /// Test register and unregister.
    public function testRegisterFunction() {
        # initially setUp has removed the motley autoloader,
        # but the class should still be defined.
        $this->assertFalse(\Motley\Autoloader::isRegistered());
        \Motley\Autoloader::register();
        $this->assertTrue(\Motley\Autoloader::isRegistered());
        \Motley\Autoloader::unregister();
        $this->assertFalse(\Motley\Autoloader::isRegistered());
        \Motley\Autoloader::register();
    }

    /// Test invoking autoload directly.
    public function testAutoload() {
        \Motley\Autoloader::unregister();
        $fakeClassName = 'NONEXISTENT\CLASS';
        $stat = \Motley\Autoloader::autoload($fakeClassName);
        $this->assertFalse($stat);
        $realClassName = 'Motley\UnitTestSupport';
        $stat = \Motley\Autoloader::autoload($realClassName);
        $this->assertTrue(class_exists($realClassName,false));
        \Motley\Autoloader::register();
    }
}
?>

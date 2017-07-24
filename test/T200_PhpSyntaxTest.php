<?php
/// Unit test class source code file.
/// @file
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.
///   MIT License. See <https://opensource.org/licenses/MIT>.

use PHPUnit\Framework\Testcase;

/// Runs 'php --syntax-check' on all php files.
class T200_PhpSyntaxTest extends Testcase {

    /// Array of relative directories with php source code to check.
    protected $sourceDirs = array(
        'phplib/Motley',
        'phpcmd',
        'test'
    );

    /// Run 'php --syntax-check' on all php files.
    public function testPhpSyntax() {
        foreach($this->sourceDirs as $sourceDir) {
            $srcDir = __DIR__ . "/../$sourceDir";
            $dirHandle = opendir($srcDir);
            $this->assertTrue($dirHandle!==FALSE);
            if ($dirHandle===FALSE) {
                $this->assertTrue($dirHandle!==FALSE,"Error on opendir($srcDir)");
                continue;
            }
            while(($file = readdir($dirHandle))!==FALSE) {
                if (substr($file,-4)==".php") {
                    $fullFile = "$srcDir/$file";
                    $cmdBase = 'php --syntax-check "' . $fullFile . '"';
                    $cmdFull = $cmdBase . " > /dev/null 2>&1";
                    $lastLine = system($cmdFull,$retVal);
                    $this->assertEquals(0,$retVal,$cmdBase);
                }
            }
            closedir($dirHandle);
        }
    }

}
?>
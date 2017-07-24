#!/usr/bin/env php
<?php
/// Convert phpunit testdox format unit test results to TAP format.
/// This file contains the a command line program which can
/// convert testdox format unit test results (e.g., from PHPUnit) to
/// TAP format results.\n
/// See <https://testanything.org/> for more TAP information.\n
/// See <https://phpunit.de/> for more PHPUnit testdox information.
/// @file
/// @verbatim
/// Usage:
///   motleyTestDoxToTap [<input-file> [<output-file>]]
/// @endverbatim

$inputFilename  = 'php://stdin';  ///< Default input.
$outputFilename = 'php://stdout'; ///< Default output.

#ifndef DOXYGEN_SHOULD_SKIP_THIS

// parse command line options
if (count($argv)>1) {
    if ($argv[1]!="-") {
        $inputFilename = $argv[1];
    }
}
if (count($argv)>2) {
    $outputFilename = $argv[2];
}
if (count($argv)>3) {
    die("ERROR: too many command line arguments.\n");
}
$lastTestClassName = "";
$testResults = array();
$testNames = array();
$iFile = fopen($inputFilename,'r');
if ($iFile===FALSE) {
    die("ERROR: could not open $inputFilename.\n");
}
while(($line = fgets($iFile))!==FALSE) {
    $line = trim($line);
    if (strlen($line)>0) {
        if(substr($line,0,1)=="[") {
            # test result line
            $doxTestResult = substr($line,0,3);
            $testName = substr($line,4);
            if($doxTestResult=="[x]") {
                $tapTestResult = "ok";
            } else {
                $tapTestResult = "not ok";
            }
            $testResults[] = $tapTestResult;
            $testNames[] = $lastTestClassName . "::" . $testName;
        } else {
            # test class name line
            $lastTestClassName = $line;
        }
    }
}
fclose($iFile);
# now output tap results
$testCount = count($testResults);
$oFile = fopen($outputFilename,'w');
if ($oFile===FALSE) {
    die("ERROR: could not open $outputFilename/\n");
}
$bytes = fwrite($oFile,"1..$testCount\n");
if ($bytes===FALSE) {
    die("ERROR: could not write to $outputFilename.\n");
}
for($idx=0; $idx<$testCount; $idx++) {
    $testNumber = $idx + 1;
    $line = "$testResults[$idx] $testNumber $testNames[$idx]\n";
    $bytes = fwrite($oFile,$line);
    if ($bytes===FALSE) {
        die("ERROR: could not write to $outputFilename.\n");
    }
}
fclose($oFile);

#endif /* DOXYGEN_SHOULD_SKIP_THIS */

exit(0);
?>

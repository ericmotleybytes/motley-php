#!/usr/bin/env php
<?php
namespace Motley;
require_once(__DIR__.'/Command.php');
require_once(__DIR__.'/CommandOpt.php');
require_once(__DIR__.'/CommandOptGrp.php');
require_once(__DIR__.'/CommandArg.php');
use Motley\Command;
use Motley\CommandOpt;
class TryCommand extends Command {
    public function __construct(string $name, string $desc) {
        parent::__construct($name,$desc);
        $helpOpt = new CommandOpt("Help","Display help screen.");
        $helpOpt->addOptSwitches(array("-h","--help"));
        $versOpt = new CommandOpt("Version","Display program version.");
        $versOpt->addOptSwitches(array("-v","--version"));
        $optGrp  = new CommandOptGrp();
        $optGrp->addOption($helpOption);
        $optGrp->addOption($versOption);
        $argFile = new CommandArg("file","Any file.");
        $argFile->setIsFile(true);
    }
    public function runcmd($argv) {
        echo "Running my command...\n";
        $idx=0;
        foreach($argv as $arg) {
            echo "argv[$idx]='$argv[$idx]'.\n";
            $idx++;
        }
    }
}
if (php_sapi_name()=="cli") {
    $cmd = new TryCommand("trycmd","Just a test to try making a command.");
    $cmd->runcmd($argv);
    exit(0);
}
?>

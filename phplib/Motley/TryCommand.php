<?php
namespace Motley;
use Motley\Command;
use Motley\CommandOpt;
use Motley\CommandArrange;
class TryCommand extends Command {
    public function __construct(string $name, string $desc) {
        parent::__construct($name,$desc);
        $helpOpt = new CommandOpt("Help","Display help screen.");
        $helpOpt->addOptSwitches(array("-h","--help"));
        $versOpt = new CommandOpt("Version","Display program version.");
        $versOpt->addOptSwitches(array("-v","--version"));
        $optGrp  = new CommandOptGrp();
        $optGrp->addOption($helpOpt);
        $optGrp->addOption($versOpt);
        $argFile = new CommandArg("file","Any file.");
        $argFile->setIsFile(true);
        $arrange = new CommandArrange();
        $arrange->addOptGrp($optGrp);
        $arrange->addArg($argFile);
        $this->addArragement($arrange);
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
?>

<?php
/// Source code file for the Motley::GuidCommand class.
/// @copyright Copyright (c) 2017, Eric Alan Christiansen.\n
/// MIT License. See <https://opensource.org/licenses/MIT>.
/// @file
### Note: This file uses Uses doxygen style annotation comments.
### Note: This file possibly includes some PHPUnit comment directives.
namespace Motley;
require_once(__DIR__ . '/CommandDoubleDash.php');
use Motley\Command;
use Motley\CommandArg;
use Motley\CommandOpt;
use Motley\CommandOptGrp;
use Motley\CommandDoubleDash;
use Motley\CommandArrange;
class GuidCommand extends Command {
    public function __construct(string $name, string $desc) {
        parent::__construct($name,$desc);
        # setup --help and --version
        $helpOpt = new CommandOpt("Help","Display help screen.");
        $helpOpt->addOptSwitches(array("-h","--help"));
        $versOpt = new CommandOpt("Version","Display program version.");
        $versOpt->addOptSwitches(array("-v","--version"));
        $loneGrp  = new CommandOptGrp("Stand Alone Options",
            "Options that must be used without other options.");
        $loneGrp->addOption($helpOpt);
        $loneGrp->addOption($versOpt);
        # setup --upper and --lower
        $lowerOpt = new CommandOpt("Lower","Use lowercase hex letters.");
        $lowerOpt->addOptSwitches(array("-l","--lower"));
        $upperOpt = new CommandOpt("Upper","Use uppercase hex letters.");
        $upperOpt->addOptSwitches(array("-u","--upper"));
        $modGrp  = new CommandOptGrp("OutputOptions",
            "Options that modify GUID representation.");
        $modGrp->addOption($lowerOpt);
        $modGrp->addOption($upperOpt);
        # a bare double dash object
        $dblDash = new CommandDoubleDash();
        # a fake argument
        $fakeArg = new CommandArg("fake","A fake argument for testing.");
        # main arrangement
        $mainArrange = new CommandArrange();
        $mainArrange->addOptGrp($modGrp,true);
        $mainArrange->addDoubleDash($dblDash,true);
        $mainArrange->addArg($fakeArg,false,true);
        $this->addArrangement($mainArrange);
        # help arrangement
        $helpArrange = new CommandArrange();
        $helpArrange->addOpt($helpOpt);
        $this->addArrangement($helpArrange);
        # version arrangement
        $versArrange = new CommandArrange();
        $versArrange->addOpt($versOpt);
        $this->addArrangement($versArrange);
    }
    public function runcmd(array $argv) : int {
        echo("Running " . $this->getCmdName() . PHP_EOL);
        $idx=0;
        foreach($argv as $arg) {
            echo("argv[$idx]='$argv[$idx]'.\n");
            $idx++;
        }
        # debug display width
        $f = new \Motley\UsageFormatter();
        $w = $f->getColumnWidth();
        echo("columnWidth=$w\n");
        # display help
        $this->displayHelp();
        return 0;
    }
}
?>

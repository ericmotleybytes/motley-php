#!/usr/bin/env php
<?php
require_once(__DIR__.'/../Motley/Command.php');
require_once(__DIR__.'/../Motley/CommandOpt.php');
require_once(__DIR__.'/../Motley/CommandOptGrp.php');
require_once(__DIR__.'/../Motley/CommandArg.php');
require_once(__DIR__.'/../Motley/CommandArrange.php');
require_once(__DIR__.'/../Motley/GuidGenerator.php');
require_once(__DIR__.'/../Motley/UsageFormatter.php');
require_once(__DIR__.'/../Motley/GuidCommand.php');
use Motley\GuidCommand;
if (php_sapi_name()=="cli") {
    $program = $argv[0];
    $progname = basename($program,".php");
    $cmd = new GuidCommand($progname,"Output a unique GUID/UUID.");
    $stat = $cmd->runcmd($argv);
    exit($stat);
} else {
    trigger_error("Not cli mode.",E_USER_ERROR);
    exit(1);
}
?>

#!/usr/bin/env php
<?php
require_once(__DIR__.'/../Motley/Autoloader.php');
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

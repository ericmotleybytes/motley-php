#!/usr/bin/env php
<?php
require_once(__DIR__.'/../phplib/Motley/Autoloader.php');
use Motley\CommandDemo;
$program = $argv[0];
$progname = basename($program,".php");
$cmd = new CommandDemo($progname,'Demonstrate Motley\Command.');
$stat = $cmd->run(true);
exit($stat);
?>

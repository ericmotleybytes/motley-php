#!/usr/bin/env php
<?php
require_once(__DIR__ . '/../phplib/Motley/GuidGenerator.php');
use Motley\GuidGenerator;
$guidGen = GuidGenerator::getGlobalGuidGenerator();
$guid = $guidGen->generateGuid();
echo "$guid\n";
exit(0);
?>

<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Pharizer\Command\Build;
use Pharizer\Command\ListFiles;
use Symfony\Component\Console\Application;

$application = new Application();
$application->setName('Pharizer');
$application->add(new Build());
$application->add(new ListFiles());
try {
    $application->run();
}
catch (Exception $e) {
    print "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
exit(0);
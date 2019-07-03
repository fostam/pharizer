<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Pharizer\Command\Build;
use Pharizer\Command\ListFiles;
use Symfony\Component\Console\Application;

$application = new Application();
$application->setName('Pharizer');
$application->setVersion('1.0.0');
$application->add(new Build());
$application->add(new ListFiles());
try {
    $application->run();
}
catch (Exception $e) {
    fwrite(STDERR, "ERROR: " . $e->getMessage() . "\n");
    exit(1);
}
exit(0);
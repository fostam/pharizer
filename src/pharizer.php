<?php

$autoload = false;
foreach([__DIR__ . '/../../../../vendor/autoload.php', __DIR__ . '/../vendor/autoload.php'] as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
        $autoload = true;
        break;
    }
}

if ($autoload !== true) {
    fwrite(STDERR, "ERROR: could not find autoload file\n");
    exit(1);
}

use Pharizer\Command\Build;
use Pharizer\Command\ListFiles;
use Symfony\Component\Console\Application;

$application = new Application();
$application->setName('Pharizer');
$application->setVersion('1.0.3');
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
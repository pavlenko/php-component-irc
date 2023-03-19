<?php

namespace PE\Component\IRC;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/vendor/autoload.php';

$logger = new ConsoleLogger(new ConsoleOutput(OutputInterface::VERBOSITY_DEBUG));

$handler = new Handler([
    new Channel('#foo'),
]);

$server = new Server('server', null, null, $logger);
$server->listen('0.0.0.0:6667');

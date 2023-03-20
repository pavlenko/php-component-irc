<?php

namespace PE\Component\IRC;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/vendor/autoload.php';

$config = new Config(
    'server[127.0.0.1:6667]',
    new \DateTime(),
    'v0.1',
    null,
    __DIR__ . '/IRCat.motd'
);

$logger = new ConsoleLogger(new ConsoleOutput(OutputInterface::VERBOSITY_DEBUG));
$server = new Server($config, null, $logger);
$server->listen('0.0.0.0:6667');

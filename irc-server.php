<?php

namespace PE\Component\IRC;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 'on');

//class KEY {};
//class VAL {};
//$key = new KEY();
//$val = new VAL();
//$map = new \SplObjectStorage();
//$map->attach($key, $val);
//$map->attach($key, $val);
//var_dump($map);
//$map->detach($key);
//var_dump($map);
//die;


$config = new Config(
    'local.dev',
    'test',
    'test',
    'test@local.dev',
    'v1.0',
    'dev',
    'testing',
    date(DATE_ATOM),
    null,
    __DIR__ . '/IRCat.motd'
);

$logger = new ConsoleLogger(new ConsoleOutput(OutputInterface::VERBOSITY_DEBUG));
$server = new Server($config, null, $logger);
$server->listen('0.0.0.0:6667');

<?php

namespace PE\Component\IRC;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\ConstStub;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\VarDumper;

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

VarDumper::setHandler(function ($var) {
    $cloner = new VarCloner();
    $cloner->addCasters([
        // Simplify output for debug
        Session::class => function (Session $obj, $arr) {
            unset($arr["\x00" . Session::class . "\x00connection"]);
            unset($arr["\x00" . Session::class . "\x00flags"]);

            $flags = [];
            foreach ((new \ReflectionObject($obj))->getConstants() as $name => $mask) {
                $flags[Caster::PREFIX_VIRTUAL . $name] = $obj->hasFlag($mask);
            }

            return $flags + $arr;
        },
        Channel::class => function (Channel $obj, $arr) {
            unset($arr["\x00" . Session::class . "\x00flags"]);
            $flags = [];
            foreach ((new \ReflectionObject($obj))->getConstants() as $name => $mask) {
                $flags[Caster::PREFIX_VIRTUAL . $name] = $obj->hasFlag($mask);
            }

            return $flags + $arr;
        },
        Server::class => function ($obj, $arr) {
            unset($arr["\x00" . Server::class . "\x00loop"]);
            unset($arr["\x00" . Server::class . "\x00socket"]);
            unset($arr["\x00" . Server::class . "\x00logger"]);
            unset($arr["\x00" . Server::class . "\x00events"]);
            return $arr;
        },
    ]);

    $dumper = new CliDumper();
    $dumper->dump($cloner->cloneVar($var));
});

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

<?php

namespace PE\Component\IRC;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
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

            if (isset($arr["\x00" . Session::class . "\x00flags"])) {
                unset($arr["\x00" . Session::class . "\x00flags"]);

                $flags = array_filter((new \ReflectionObject($obj))->getConstants(), [$obj, 'hasFlag']);
                $flags = array_map(fn($f) => substr($f, 5), array_flip($flags));

                $arr = ["\x00" . Session::class . "\x00flags" => new ConstStub(implode('|', $flags))] + $arr;
            }

            return $arr;
        },
        Channel::class => function (Channel $obj, $arr) {
            if (isset($arr["\x00" . Channel::class . "\x00flags"])) {
                unset($arr["\x00" . Channel::class . "\x00flags"]);

                $flags = array_filter((new \ReflectionObject($obj))->getConstants(), [$obj, 'hasFlag']);
                $flags = array_map(fn($f) => substr($f, 5), array_flip($flags));

                $arr = ["\x00" . Channel::class . "\x00flags" => new ConstStub(implode('|', $flags))] + $arr;
            }

            return $arr;
        },
        Server::class => function ($obj, $arr) {
            unset($arr["\x00" . Server::class . "\x00loop"]);
            unset($arr["\x00" . Server::class . "\x00config"]);
            unset($arr["\x00" . Server::class . "\x00socket"]);
            unset($arr["\x00" . Server::class . "\x00logger"]);
            unset($arr["\x00" . Server::class . "\x00events"]);
            return $arr;
        },
    ]);

    $dumper = new CliDumper();
    $dumper->dump($cloner->cloneVar($var));
});

$logger = new ConsoleLogger(new ConsoleOutput(OutputInterface::VERBOSITY_DEBUG));
$server = new Server(__DIR__ . '/irc-config.php', $logger);
$server->listen('0.0.0.0:6667');

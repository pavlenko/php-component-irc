<?php

namespace PE\Component\IRC;

use PE\Component\Event\Emitter;
use PE\Component\IRC\Client\Client;
use PE\Component\IRC\Client\ClientConfig;
use PE\Component\IRC\Event\ConnectedEvent;
use PE\Component\IRC\Protocol\Factory;
use PE\Component\IRC\Client\UserAPI;
use PE\Component\Socket\Factory as SocketFactory;
use PE\Component\Socket\Select as SocketSelect;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$config = ClientConfig::forUser(null, 'master__', 'phpbot', SessionInterface::FLAG_RECEIVE_WALLOPS, 'php IRC bot test');

$factory = new Factory(new SocketFactory(new SocketSelect()));
$emitter = new Emitter();

$logger = new ConsoleLogger(new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG));
$client = new Client($config, $factory, $emitter, $logger);

//$emitter->attach('message', function (MSG $msg, \PE\Component\IRC\Protocol\Connection $connection) use ($config) {
//    if ($msg->getCode() === sprintf('%03d', RPL::WELCOME)) {
//        dump($msg);
//        //$connection->send(new CMD(CMD::LIST));
//        $connection->send(new CMD(CMD::NAMES));
//        $connection->send(new CMD(CMD::WHOIS, [$config->nickname]));
//    }
//});

$client->attach(ConnectedEvent::class, function (ConnectedEvent $event) {
    $user = new UserAPI($event->getConnection());
    $user->register(null, 'master__', 'phpbot', 'php IRC bot test', 0b1000)
        ->onSuccess(fn() => $event->getConnection()->send(new CMD(CMD::WHOIS, ['master__'])));
});
$client->connect('tls://irc.libera.chat:6697');
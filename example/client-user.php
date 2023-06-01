<?php

namespace PE\Component\IRC;

use PE\Component\IRC\Client\Client;
use PE\Component\IRC\Client\ClientAPI;
use PE\Component\IRC\Event\ConnectedEvent;
use PE\Component\IRC\Protocol\Factory;
use PE\Component\Socket\Factory as SocketFactory;
use PE\Component\Socket\Select as SocketSelect;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__ . '/../vendor/autoload.php';

$factory = new Factory(new SocketFactory(new SocketSelect()));
$logger  = new ConsoleLogger(new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG));
$client  = new Client($factory, $logger);

$client->attach(ConnectedEvent::class, function (ConnectedEvent $event) {
    $conn = $event->getConnection();

    $api = new ClientAPI($conn, new Session(null, $conn->getRemoteAddress(), $conn->getClientAddress()));
    $api->USER(null, 'master__', 'phpbot', 'php IRC bot test', 0b1000)
        ->then(function () use ($conn, $api) {
            //$conn->send(new CMD(CMD::WHOIS, ['master__']));
            $api->QUIT();
        });
});
$client->connect('tls://irc.libera.chat:6697');

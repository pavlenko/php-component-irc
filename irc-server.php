<?php

namespace PE\Component\IRC;

use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

require_once __DIR__ . '/vendor/autoload.php';

$loop = Loop::get();

/* allowed to use ports 6660...7000 */
$socket = new SocketServer('0.0.0.0:6667', [], $loop);
$socket->on('connection', function (ConnectionInterface $connection) {
    //$connection->write("Hello " . $connection->getRemoteAddress() . "!\n");
    //$connection->write("Welcome to this amazing server!\n");
    //$connection->write("Here's a tip: don't say anything.\n");
    //Connection per client

    echo "CONNECTED\n";
    $connection->on('data', function ($data) use ($connection) {
        dump((new Parser())->parse($data));
        //$connection->close();
    });
    $connection->on('close', function () {
        echo "EXIT\n";
    });
});
echo 'Listening on ' . $socket->getAddress() . PHP_EOL;

//TODO check stop function
$stop_func = function ($signal) use ($loop, $socket, &$stop_func) {
    echo 'Signal: ', (string)$signal, PHP_EOL;
    $loop->removeSignal($signal, $stop_func);

    echo 'Shutting down server socket' . PHP_EOL;
    //$socket->close();
    echo 'Shutted down server socket' . PHP_EOL;
    $loop->stop();
};

$loop->addSignal(SIGINT, $stop_func);
$loop->addSignal(SIGTERM, $stop_func);
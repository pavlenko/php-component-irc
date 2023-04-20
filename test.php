<?php

namespace PE\Component\IRC;

use PE\Component\Stream\Factory;
use PE\Component\Stream\Select;
use PE\Component\Stream\Socket;
use PE\Component\Stream\Stream;

require_once __DIR__ . '/vendor/autoload.php';

$active = true;
$stream = (new Factory())->createClient('tls://irc.libera.chat:6697');
$select = new Select();
$select->attachStreamWR($stream, function (Stream $stream, Select $select) use (&$active) {
    $select->detachStreamWR($stream);
    echo "!: Connected to remote {$stream->getAddress(true)}\n";

    $socket = new Socket($stream, $select);
    $socket->onInput(function ($message) {
        echo $message . "\n";
    });
    $socket->onError(function ($message) {
        echo $message . "\n";
    });
    $socket->onClose(function ($message) use (&$active) {
        $active = false;
        echo $message . "\n";
    });
    $socket->write((new CMD(CMD::PASSWORD, ['pass', '0201', 'IRC|']))->toString() . "\n");
    $socket->write((new CMD(CMD::SERVER, ['srv0', '0', '999'], 'just test'))->toString() . "\n");
});

while ($active) {
    $select->dispatch();
    usleep(1000);
}

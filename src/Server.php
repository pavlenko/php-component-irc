<?php

namespace PE\Component\IRC;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

class Server
{
    private Handler $handler;
    private Parser $parser;
    private \SplObjectStorage $sessions;

    private ?SocketServer $socket = null;
    private LoopInterface $loop;
    private LoggerInterface $logger;

    public function __construct(LoopInterface $loop = null, LoggerInterface $logger = null)
    {
        $this->handler  = new Handler();
        $this->parser   = new Parser();
        $this->sessions = new \SplObjectStorage();

        $this->loop   = $loop ?: Loop::get();
        $this->logger = $logger ?: new NullLogger();
    }

    public function listen(string $address): void
    {
        $this->loop->addSignal(SIGINT, [$this, 'stop']);
        $this->loop->addSignal(SIGTERM, [$this, 'stop']);

        $this->socket = new SocketServer($address, [], $this->loop);
        $this->socket->on('connection', function (ConnectionInterface $connection) {
            $this->logger->info('New connection from ' . $connection->getRemoteAddress());
            $this->sessions->attach($connection, new Session($connection, $this, ['addr' => $connection->getRemoteAddress()]));

            $connection->on('data', fn($data) => $this->processMessageReceived($connection, $this->parser->parse($data)));

            //TODO on close, on error
            $connection->on('close', function () {
                echo "closed\n";
            });
        });

        $this->logger->info('Listening on ' . $this->socket->getAddress());
    }

    private function processMessageReceived(ConnectionInterface $connection, Command $command)
    {
        $this->logger->info('<-- ' . $command);
        $this->handler->handle($command, $this->sessions[$connection], $this);
    }

    public function processMessageSend(ConnectionInterface $connection, Command $command)
    {
        $this->logger->info('--> ' . $command);
        $connection->write($command);
    }

    public function stop(int $signal = null)
    {
        $this->logger->info('Stopping server ...');
        if (null !== $this->socket) {
            $this->socket->close();
        }
        if (null !== $signal) {
            $this->loop->removeSignal($signal, [$this, 'stop']);
        }
        $this->loop->stop();
        $this->logger->info('Stopping server OK');
    }
}
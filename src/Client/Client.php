<?php

namespace PE\Component\IRC\Client;

use PE\Component\Event\EmitterInterface;
use PE\Component\Event\EmitterTrait;
use PE\Component\Event\Event;
use PE\Component\IRC\CMD;
use PE\Component\IRC\Event\ConnectedEvent;
use PE\Component\IRC\MSG;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\Protocol\Factory;
use PE\Component\Loop\LoopInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

final class Client implements ClientInterface, EmitterInterface
{
    use EmitterTrait;

    private Factory $factory;
    private LoggerInterface $logger;
    private LoopInterface $loop;

    private ?Connection $connection = null;

    public function __construct(
        Factory $factory,
        LoggerInterface $logger = null
    ) {
        $this->factory = $factory;
        $this->logger  = $logger ?: new NullLogger();

        $this->loop = $factory->createLoop(function () {
            if ($this->connection) {
                $this->connection->tick();
            }
        });
    }

    public function connect(string $address, array $context = [], ?float $timeout = null): void
    {
        $this->connection = $this->factory->createConnection(
            $this->factory->createSocketClient($address, $context, $timeout)
        );

        $this->connection->attach(Connection::ON_INPUT, function (MSG $msg) {
            $this->logger->log(LogLevel::NOTICE, 'I: ' . $msg->toLogger());
            $this->processReceive($this->connection, $msg);
        });

        $this->connection->attach(Connection::ON_WRITE, function (MSG $msg) {
            $this->logger->log(LogLevel::NOTICE, 'O: ' . $msg->toLogger());
        });

        $this->connection->attach(Connection::ON_ERROR, function (\Throwable $exception) {
            $this->logger->log(LogLevel::ERROR, 'E: ' . $exception->getCode() . ': ' . $exception->getMessage());
            $this->logger->log(LogLevel::DEBUG, 'E: ' . $exception->getTraceAsString());
            $this->processErrored($this->connection, $exception);
        });

        $this->connection->attach(Connection::ON_CLOSE, function (string $message = null) {
            $this->logger->log(
                LogLevel::DEBUG,
                "Connection to {$this->connection->getRemoteAddress()} closed" . ($message ? ': ' . $message : '')
            );
            $this->loop->stop();
            //$this->connection->setStatus(ConnectionInterface::STATUS_CLOSED);
        });

        $this->dispatch(new ConnectedEvent($this->connection));
        $this->loop->run();
    }

    private function processReceive(Connection $connection, MSG $msg): void
    {
        if ($msg->getCode() === CMD::PING) {
            $connection->send(new CMD(CMD::PONG, [$msg->getArg(0)]));
        }

        $this->dispatch(new Event('message', $msg, $connection));
        $this->dispatch(new Event($msg->getCode(), $msg, $connection));
    }

    private function processErrored(Connection $connection, \Throwable $exception): void
    {
        //TODO
    }

    /* @deprecated */
    public function wait(): void
    {
        $this->loop->run();
    }

    public function exit(): void
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

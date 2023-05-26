<?php

namespace PE\Component\IRC\Client;

use PE\Component\Event\Emitter;
use PE\Component\Event\Event;
use PE\Component\IRC\CMD;
use PE\Component\IRC\Deferred;
use PE\Component\IRC\MSG;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\Protocol\Factory;
use PE\Component\IRC\RPL;
use PE\Component\Loop\LoopInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

final class Client implements ClientInterface
{
    private ClientConfig $config;
    private Factory $factory;
    private Emitter $emitter;
    private LoggerInterface $logger;
    private LoopInterface $loop;

    private ?Connection $connection = null;

    public function __construct(
        ClientConfig $config,
        Factory $factory,
        Emitter $emitter,
        LoggerInterface $logger = null
    ) {
        $this->config  = $config;
        $this->factory = $factory;
        $this->emitter = $emitter;
        $this->logger  = $logger ?: new NullLogger();

        $this->loop = $factory->createLoop(fn() => null);//TODO pass loop instead of create in factory?
    }

    public function connect(string $address, array $context = [], ?float $timeout = null): Deferred
    {
        $this->connection = $this->factory->createConnection(
            $this->factory->createSocketClient($address, $context, $timeout)
        );

        $this->connection->setInputHandler(function (MSG $msg) {
            $this->logger->log(LogLevel::NOTICE, 'I: ' . $msg->toLogger());
            $this->processReceive($this->connection, $msg);
        });

        $this->connection->setWriteHandler(function (MSG $msg) {
            $this->logger->log(LogLevel::NOTICE, 'O: ' . $msg->toLogger());
        });

        $this->connection->setErrorHandler(function (\Throwable $exception, $line = null) {
            dump($line);
            $this->logger->log(LogLevel::ERROR, 'E: ' . $exception->getCode() . ': ' . $exception->getMessage());
            $this->logger->log(LogLevel::DEBUG, 'E: ' . $exception->getTraceAsString());
            $this->processErrored($this->connection, $exception);
        });

        $this->connection->setCloseHandler(function (string $message = null) {
            $this->logger->log(
                LogLevel::DEBUG,
                "Connection to {$this->connection->getRemoteAddress()} closed" . ($message ? ': ' . $message : '')
            );
            $this->loop->stop();
            //$this->connection->setStatus(ConnectionInterface::STATUS_CLOSED);
        });

        if (null !== $this->config->password) {
            $this->connection->send(new CMD(CMD::PASSWORD, [$this->config->password]));
        }

        if (ClientConfig::TYPE_USER === $this->config->type) {
            $this->connection->send(new CMD(CMD::NICK, [$this->config->nickname]));
            $this->connection->send(new CMD(
                CMD::USER,
                [$this->config->username, $this->config->usermode, '*'],
                $this->config->realname
            ));

            return $this->connection->wait(RPL::WELCOME);
        }

        if (ClientConfig::TYPE_SERVICE === $this->config->type) {
            $this->connection->send(new CMD(
                CMD::SERVICE,
                [$this->config->nickname, 0, $this->config->servers, 0, 0],
                $this->config->info
            ));

            return $this->connection->wait(RPL::YOU_ARE_SERVICE);
        }

        throw new \UnexpectedValueException(
            'Invalid client mode, allowed only ' . json_encode(ClientConfig::TYPES) . ', got: ' . $this->config->type
        );
    }

    private function processReceive(Connection $connection, MSG $msg): void
    {
        $this->emitter->dispatch(new Event('message', $msg, $connection));
        $this->emitter->dispatch(new Event($msg->getCode(), $msg, $connection));
    }

    private function processErrored(Connection $connection, \Throwable $exception): void
    {
        //TODO
    }

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

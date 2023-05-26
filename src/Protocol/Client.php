<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Deferred;
use PE\Component\IRC\MSG;
use PE\Component\IRC\RPL;
use PE\Component\Loop\LoopInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

final class Client implements ClientInterface
{
    private ClientConfig $config;
    private Factory $factory;
    private LoggerInterface $logger;
    private LoopInterface $loop;
    private Connection $connection;

    public function __construct(ClientConfig $config, Factory $factory)
    {
        $this->config  = $config;
        $this->factory = $factory;
        $this->logger  = new NullLogger();//TODO

        $this->loop = $factory->createLoop(fn() => null);
    }

    public function connect(): Deferred
    {
        //TODO tmp
        $config = [
            'socket' => [
                'address' => 'tcp://127.0.0.1:6697',
                'context' => [],
                'timeout' => null,
            ],
            'client' => [
                'type'     => 'client',// client|service
                'password' => null,// client|service
                'nickname' => 'mAsTeR',// client|service
                'username' => 'mAsTeR',// client
                'realname' => 'mAsTeR',// client
                'servers'  => '',// service, servers mask
                'mode'     => 0,// client, Bitmask
                'info'     => '',// service
            ],
        ];

        $this->connection = $this->factory->createConnection($this->factory->createSocketClient(
            $config['socket']['address'],
            $config['socket']['context'],
            $config['socket']['timeout'],
        ));

        $this->connection->setInputHandler(function (MSG $msg) {
            $this->logger->log(LogLevel::NOTICE, 'I: ' . $msg->toLogger());
            $this->processReceive($this->connection, $msg);
        });

        $this->connection->setWriteHandler(function (MSG $msg) {
            $this->logger->log(LogLevel::NOTICE, 'O: ' . $msg->toLogger());
        });

        $this->connection->setErrorHandler(function (\Throwable $exception) {
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

        if ('client' === $this->config->type) {
            $this->connection->send(new CMD(CMD::NICK, [$this->config->nickname]));
            $this->connection->send(new CMD(
                CMD::USER,
                [$this->config->username, $this->config->usermode, '*'],
                $this->config->realname
            ));

            return $this->connection->wait(RPL::WELCOME);
        }

        if ('service' === $this->config->type) {
            $this->connection->send(new CMD(
                CMD::SERVICE,
                [$this->config->nickname, 0, $this->config->servers, 0, 0],
                $this->config->info
            ));

            return $this->connection->wait(RPL::YOU_ARE_SERVICE);
        }

        throw new \UnexpectedValueException(
            'Invalid client mode, allowed only "client" or "service", got: ' . $this->config->type
        );
    }

    private function processReceive(Connection $connection, MSG $msg): void
    {
        //TODO
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
        // TODO: Implement exit() method.
    }
}

<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\Loop\Loop;
use PE\Component\Loop\LoopInterface;
use PE\Component\Socket\Client as SocketClient;
use PE\Component\Socket\Factory as SocketFactory;
use PE\Component\Socket\Server as SocketServer;

final class Factory
{
    private SocketFactory $factory;

    public function __construct(SocketFactory $factory)
    {
        $this->factory = $factory;
    }

    public function createLoop(callable $tickHandler): LoopInterface
    {
        return new Loop(1, function () use ($tickHandler) {
            $this->factory->getSelect()->dispatch();
            call_user_func($tickHandler);
        });
    }

    public function createConnection(SocketClient $client): Connection
    {
        return new Connection($client);
    }

    public function createSocketClient(string $address, array $context = [], ?float $timeout = null): SocketClient
    {
        return $this->factory->createClient($address, $context, $timeout);
    }

    public function createSocketServer(string $address, array $context = []): SocketServer
    {
        return $this->factory->createServer($address, $context);
    }
}

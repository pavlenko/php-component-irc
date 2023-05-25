<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\Loop\Loop;
use PE\Component\Loop\LoopInterface;

class Factory
{
    private \PE\Component\Socket\Factory $factory;

    public function __construct(\PE\Component\Socket\Factory $factory)
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



    public function createSocketClient(
        string $address,
        array $context = [],
        ?float $timeout = null
    ): \PE\Component\Socket\Client {
        return $this->factory->createClient($address, $context, $timeout);
    }
}

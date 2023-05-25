<?php

namespace PE\Component\IRC\Protocol;

use PE\Component\IRC\Deferred;
use PE\Component\Loop\LoopInterface;

final class Client implements ClientInterface
{
    private Factory $factory;
    private LoopInterface $loop;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
        $this->loop    = $factory->createLoop(function () {
            //TODO some dispatch logic
        });
    }

    public function connect(string $uri): Deferred
    {

    }

    public function wait(): void
    {
        // TODO: Implement wait() method.
    }

    public function exit(): void
    {
        // TODO: Implement exit() method.
    }
}

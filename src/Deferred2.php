<?php

namespace PE\Component\IRC;

//TODO write example code usages for multi-response commands
class Deferred2
{
    public function __construct(int $timeout, string ...$expectCode)
    {}

    public function then(callable $handler)
    {}

    public function else(callable $handler)
    {}

    public function settle($value): void
    {}

    public function wait($loop)
    {}
}

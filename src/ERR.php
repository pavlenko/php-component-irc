<?php

namespace PE\Component\IRC;

class ERR extends MSG
{
    public function __construct(string $prefix, int $code, array $args = [], string $comment = null)
    {
        parent::__construct($code, $args, $comment, $prefix);
    }

    public function __toString(): string
    {
        return '';// TODO: Implement __toString() method.
    }
}

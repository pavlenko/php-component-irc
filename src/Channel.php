<?php

namespace PE\Component\IRC;

class Channel
{
    // Modify "o","l","b","v","k" flags has separate logic
    public const FLAG_PRIVATE     = 0b000001;//p - private channel
    public const FLAG_SECRET      = 0b000010;//s - secret channel
    public const FLAG_MODERATED   = 0b000100;//m - moderated channel
    public const FLAG_INVITE_ONLY = 0b001000;//i - invite only channel
    public const FLAG_TOPIC_SET   = 0b010000;//t - topic settable by channel operator only
    public const FLAG_NO_MSG_OUT  = 0b100000;//n - no messages to channel from clients on the outside

    private string $name;
    private ?string $pass;

    public function __construct(string $name, ?string $pass = null)
    {
        $this->name = $name;
        $this->pass = $pass;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function getUsers(): array
    {
        return [];//TODO
    }

    public function getFlags(): string
    {
        return '';//TODO
    }

    public function getTopic(): string
    {
        return '';//TODO
    }
}
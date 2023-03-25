<?php

namespace PE\Component\IRC;

interface SessionInterface
{
    public const FLAG_CAP_RESOLVED     = 0b000000001;
    public const FLAG_REGISTERED       = 0b000000010;
    public const FLAG_INVISIBLE        = 0b000000100;
    public const FLAG_RECEIVE_NOTICE   = 0b000001000;
    public const FLAG_RECEIVE_WALLOPS  = 0b000010000;
    public const FLAG_IS_OPERATOR      = 0b000100000;
    public const FLAG_AWAY             = 0b001000000;
    public const FLAG_PINGING          = 0b010000000;
    public const FLAG_BREAK_CONNECTION = 0b100000000;

    public function __construct(string $servername, string $hostname);
    public function getServername(): string;
    public function getPassword(): string;
    public function setPassword(string $password): void;
    public function getNickname(): string;
    public function setNickname(string $nickname): void;
    public function getUsername(): string;
    public function setUsername(string $username): void;
    public function getHostname(): string;
    public function setHostname(string $hostname): void;
    public function getRealname(): string;
    public function setRealname(string $realname): void;
    public function getFlags(): int;
    public function hasFlag(int $flag): bool;
    public function setFlag(int $flag): void;
    public function clrFlag(int $flag): void;
    public function getPrefix(): string;
    public function getAwayMessage(): string;
    public function setAwayMessage(string $message): void;
    public function getQuitMessage(): string;
    public function setQuitMessage(string $message): void;
    public function getChannels(): ChannelMap;
    public function attachChannel(Channel $channel): void;
    public function detachChannel(Channel $channel): void;
}
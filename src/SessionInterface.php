<?php

namespace PE\Component\IRC;

interface SessionInterface
{
    //TODO mark session by type after success registration
    public const TYPE_CLIENT  = 'CLIENT';
    public const TYPE_SERVER  = 'SERVER';
    public const TYPE_SERVICE = 'SERVICE';

    public const FLAG_REGISTERED       = 0b00000001;
    public const FLAG_INVISIBLE        = 0b00000010;
    public const FLAG_RECEIVE_NOTICE   = 0b00000100;
    public const FLAG_RECEIVE_WALLOPS  = 0b00001000;
    public const FLAG_IRC_OPERATOR     = 0b00010000;
    public const FLAG_AWAY             = 0b00100000;
    public const FLAG_PINGING          = 0b01000000;
    public const FLAG_BREAK_CONNECTION = 0b10000000;

    public function __construct(ConnectionInterface $connection, string $servername, string $hostname);

    public function get(string $key, $default = null);
    public function set(string $key, $value): void;
    public function has(string $key): bool;
    public function del(string $key): void;

    public function sendCMD(string $code, array $args = [], string $comment = null, string $prefix = null): bool;
    public function sendERR(int $code, array $args = [], string $comment = null): bool;
    public function sendRPL(int $code, array $args = [], string $comment = null): bool;
    public function close(): void;

    public function numChannels(): int;

    /**
     * @param StorageInterface $storage
     * @return ChannelInterface[]
     */
    public function getChannels(StorageInterface $storage): array;
    public function hasChannel(ChannelInterface $channel): bool;
    public function addChannel(ChannelInterface $channel): void;
    public function delChannel(ChannelInterface $channel): void;

    public function getType(): ?string;
    public function setType(string $type): void;

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
    public function getFlagsAsString(): string;
    public function hasFlag(int $flag): bool;
    public function setFlag(int $flag): void;
    public function clrFlag(int $flag): void;
    public function getPrefix(): string;
    public function getAwayMessage(): string;
    public function setAwayMessage(string $message): void;
    public function getQuitMessage(): string;
    public function setQuitMessage(string $message): void;
    public function getLastMessageTime(): int;
    public function updLastMessageTime(): void;
    public function getLastPingingTime(): int;
    public function updLastPingingTime(): void;
    public function getRegistrationTime(): int;
}

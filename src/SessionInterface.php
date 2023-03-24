<?php

namespace PE\Component\IRC;

interface SessionInterface
{
    public function __construct(Connection $connection, string $servername, string $hostname);
    public function getServername(): string;
    public function getPassword(): string;
    public function setPassword(string $password): void;
    public function getNickname(): string;
    public function setNickname(string $password): void;
    public function getUsername(): string;
    public function setUsername(string $password): void;
    public function getHostname(): string;
    public function setHostname(string $password): void;
    public function getRealname(): string;
    public function setRealname(string $password): void;
    public function getFlags(): int;
    public function setFlag(int $flag): void;
    public function clrFlag(int $flag): void;
    public function getPrefix(): string;
    public function getAwayMessage(): string;
    public function setAwayMessage(string $message): void;
    public function getQuitMessage(): string;
    public function setQuitMessage(string $message): void;
    public function getChannels(): array;
    public function attachChannel(Channel $channel): void;
    public function detachChannel(Channel $channel): void;
}
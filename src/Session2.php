<?php

namespace PE\Component\IRC;

class Session2 implements SessionInterface
{
    private string $servername;
    private string $hostname;
    private string $password = '';
    private string $nickname = '';
    private string $username = '';
    private string $realname = '';

    public function __construct(Connection $connection, string $servername, string $hostname)
    {
        $this->servername = $servername;
        $this->hostname   = $hostname;
    }

    public function getServername(): string
    {
        return $this->servername;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getNickname(): string
    {
        // TODO: Implement getNickname() method.
    }

    public function setNickname(string $password): void
    {
        // TODO: Implement setNickname() method.
    }

    public function getUsername(): string
    {
        // TODO: Implement getUsername() method.
    }

    public function setUsername(string $password): void
    {
        // TODO: Implement setUsername() method.
    }

    public function getHostname(): string
    {
        // TODO: Implement getHostname() method.
    }

    public function setHostname(string $password): void
    {
        // TODO: Implement setHostname() method.
    }

    public function getRealname(): string
    {
        // TODO: Implement getRealname() method.
    }

    public function setRealname(string $password): void
    {
        // TODO: Implement setRealname() method.
    }

    public function getFlags(): int
    {
        // TODO: Implement getFlags() method.
    }

    public function hasFlag(int $flag): bool
    {
        // TODO: Implement hasFlag() method.
    }

    public function setFlag(int $flag): void
    {
        // TODO: Implement setFlag() method.
    }

    public function clrFlag(int $flag): void
    {
        // TODO: Implement clrFlag() method.
    }

    public function getPrefix(): string
    {
        // TODO: Implement getPrefix() method.
    }

    public function getAwayMessage(): string
    {
        // TODO: Implement getAwayMessage() method.
    }

    public function setAwayMessage(string $message): void
    {
        // TODO: Implement setAwayMessage() method.
    }

    public function getQuitMessage(): string
    {
        // TODO: Implement getQuitMessage() method.
    }

    public function setQuitMessage(string $message): void
    {
        // TODO: Implement setQuitMessage() method.
    }

    public function getChannels(): array
    {
        // TODO: Implement getChannels() method.
    }

    public function attachChannel(Channel $channel): void
    {
        // TODO: Implement attachChannel() method.
    }

    public function detachChannel(Channel $channel): void
    {
        // TODO: Implement detachChannel() method.
    }
}
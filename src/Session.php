<?php

namespace PE\Component\IRC;

final class Session implements SessionInterface
{
    private ConnectionInterface $connection;

    private string $servername;
    private string $hostname;
    private string $password = '';
    private string $nickname = '';
    private string $username = '';
    private string $realname = '';

    private int $flags = 0;

    private string $awayMessage = '';
    private string $quitMessage = '';

    private ChannelMap $channels;

    public function __construct(ConnectionInterface $connection, string $servername, string $hostname)
    {
        $this->connection = $connection;
        $this->servername = $servername;
        $this->hostname   = $hostname;

        $this->channels = new ChannelMap();

        $this->setFlag(self::FLAG_CAP_RESOLVED);// <-- set capabilities resolved for not supported by clients
    }

    public function sendCMD(CMD $cmd): bool
    {
        return $this->connection->write($cmd);
    }

    public function sendERR(ERR $err): bool
    {
        return $this->connection->write($err);
    }

    public function sendRPL(RPL $rpl): bool
    {
        return $this->connection->write($rpl);
    }

    public function close(): void
    {
        $this->logger->notice(self::EVT_CLOSE);
        $this->socket->close();
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

        return $this->nickname;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): void
    {
        $this->hostname = $hostname;
    }

    public function getRealname(): string
    {
        return $this->realname;
    }

    public function setRealname(string $realname): void
    {
        $this->realname = $realname;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function hasFlag(int $flag): bool
    {
        return $this->flags & $flag;
    }

    public function setFlag(int $flag): void
    {
        $this->flags |= $flag;
    }

    public function clrFlag(int $flag): void
    {
        $this->flags &= ~$flag;
    }

    public function getPrefix(): string
    {
        return $this->nickname . "!" . $this->username . "@" . $this->hostname;
    }

    public function getAwayMessage(): string
    {
        return $this->awayMessage;
    }

    public function setAwayMessage(string $message): void
    {
        $this->awayMessage = $message;
    }

    public function getQuitMessage(): string
    {
        return $this->quitMessage;
    }

    public function setQuitMessage(string $message): void
    {
        $this->quitMessage = $message;
    }

    public function getChannels(): ChannelMap
    {
        return $this->channels;
    }

    public function attachChannel(Channel $channel): void
    {
        $this->channels->attach($channel);
    }

    public function detachChannel(Channel $channel): void
    {
        $this->channels->detach($channel);
    }
}
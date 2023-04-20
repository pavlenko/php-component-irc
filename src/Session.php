<?php

namespace PE\Component\IRC;

final class Session implements SessionInterface
{
    private ConnectionInterface $connection;

    private array $data = [];

    private ?string $type = null;
    private string $servername;
    private string $hostname;
    private string $password = '';
    private string $nickname = '';
    private string $username = '';
    private string $realname = '';

    private int $flags = 0;

    private string $awayMessage = '';
    private string $quitMessage = '';
    private int $lastMessageTime = 0;
    private int $lastPingingTime = 0;
    private int $registrationTime;

    private array $channels = [];

    public function __construct(ConnectionInterface $connection, string $servername, string $hostname)
    {
        $this->connection = $connection;
        $this->servername = $servername;
        $this->hostname   = $hostname;

        $this->registrationTime = time();
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function del(string $key): void
    {
        unset($this->data[$key]);
    }

    public function sendCMD(string $code, array $args = [], string $comment = null, string $prefix = null): bool
    {
        $cmd = new CMD($code, $args, $comment, $prefix);
        return $this->connection->write($cmd);
    }

    public function sendERR(int $code, array $args = [], string $comment = null): bool
    {
        $err = new ERR($this->getServername(), $code, [$this->getNickname(), ...$args], $comment);
        return $this->connection->write($err);
    }

    public function sendRPL(int $code, array $args = [], string $comment = null): bool
    {
        $rpl = new RPL($this->getServername(), $code, [$this->getNickname(), ...$args], $comment);
        return $this->connection->write($rpl);
    }

    public function close(): void
    {
        //TODO detach from all automatically
        $this->connection->close();
    }

    public function numChannels(): int
    {
        return count($this->channels);
    }

    public function getChannels(StorageInterface $storage): array
    {
        return array_filter(iterator_to_array($storage->channels()), fn($i) => $this->hasChannel($i));
    }

    public function hasChannel(ChannelInterface $channel): bool
    {
        return array_key_exists(spl_object_hash($channel), $this->channels);
    }

    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels[spl_object_hash($channel)] = spl_object_id($channel);
        }
    }

    public function delChannel(ChannelInterface $channel): void
    {
        unset($this->channels[spl_object_hash($channel)]);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
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

    public function getFlagsAsString(): string
    {
        $flags = '+';
        if ($this->hasFlag(self::FLAG_INVISIBLE)) {
            $flags .= 'i';
        }
        if ($this->hasFlag(self::FLAG_RECEIVE_NOTICE)) {
            $flags .= 's';
        }
        if ($this->hasFlag(self::FLAG_RECEIVE_WALLOPS)) {
            $flags .= 'w';
        }
        if ($this->hasFlag(self::FLAG_IRC_OPERATOR)) {
            $flags .= 'o';
        }
        return $flags;
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

    public function getLastMessageTime(): int
    {
        return $this->lastMessageTime;
    }

    public function updLastMessageTime(): void
    {
        $this->lastMessageTime = time();
    }

    public function getLastPingingTime(): int
    {
        return $this->lastPingingTime;
    }

    public function updLastPingingTime(): void
    {
        $this->lastPingingTime = time();
    }

    public function getRegistrationTime(): int
    {
        return $this->registrationTime;
    }
}
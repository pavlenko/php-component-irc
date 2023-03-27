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

    private SessionInterface $creator;
    private string $name;
    private string $pass;
    private string $topic = '';
    private int $limit = 0;
    private int $flags = 0;
    private SessionMap $sessions;
    private array $operators = [];

    public function __construct(SessionInterface $creator, string $name, string $pass = '')
    {
        $this->creator = $creator;
        $this->name    = $name;
        $this->pass    = $pass;

        //TODO add to users
        //TODO add to operators
        //TODO send info

        $this->sessions = new SessionMap();
    }

    public function getCreator(): SessionInterface
    {
        return $this->creator;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function setPass(string $pass): void
    {
        $this->pass = $pass;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
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

    public function getSessions(): SessionMap
    {
        return $this->sessions;
    }

    //TODO needed?
    public function attachSession(Connection $conn, SessionInterface $sess): void
    {
        $this->sessions->attach($conn, $sess);
    }

    //TODO needed?
    public function detachSession(Connection $conn): void
    {
        $this->sessions->detach($conn);
    }
}
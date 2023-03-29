<?php

namespace PE\Component\IRC;

final class Channel implements ChannelInterface
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

    /** @var string[] */
    private array $banMasks = [];

    private SessionMap $sessions;
    private SessionMap $speakers;
    private SessionMap $operators;
    private SessionMap $invited;

    public function __construct(SessionInterface $creator, string $name, string $pass = '')
    {
        $this->creator = $creator;
        $this->name    = $name;
        $this->pass    = $pass;

        $this->sessions  = new SessionMap();
        $this->speakers  = new SessionMap();
        $this->operators = new SessionMap();
        $this->invited   = new SessionMap();

        $this->sessions->attach($creator);
        $this->operators->attach($creator);

        $this->setFlag(self::FLAG_NO_MSG_OUT);

        //TODO sendInfo()
    }

    public function sessions(): SessionMap
    {
        return $this->sessions;
    }

    public function speakers(): SessionMap
    {
        return $this->speakers;
    }

    public function operators(): SessionMap
    {
        return $this->operators;
    }

    public function invited(): SessionMap
    {
        return $this->invited;
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

    public function getFlagsAsString(): string
    {
        $flags = '';
        if ($this->hasFlag(self::FLAG_INVITE_ONLY)) {
            $flags .= 'i';
        }
        if ($this->hasFlag(self::FLAG_NO_MSG_OUT)) {
            $flags .= 'n';
        }
        if ($this->hasFlag(self::FLAG_PRIVATE)) {
            $flags .= 'p';
        }
        if ($this->hasFlag(self::FLAG_SECRET)) {
            $flags .= 's';
        }
        if ($this->hasFlag(self::FLAG_TOPIC_SET)) {
            $flags .= 't';
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
}
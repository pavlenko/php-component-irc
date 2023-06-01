<?php

namespace PE\Component\IRC\Client;

use PE\Component\IRC\CMD;
use PE\Component\IRC\ERR;
use PE\Component\IRC\Exception\ProtocolException;
use PE\Component\IRC\MSG;
use PE\Component\IRC\SessionInterface;
use PE\Component\IRC\Util\Deferred;
use PE\Component\IRC\Protocol\Connection;
use PE\Component\IRC\RPL;

//TODO represent only registered client API, other move to: server|user|channel|registration apis
//TODO maybe create commands as USER & SERVICE but as wrapper with full logic
//TODO maybe create separate commands for change user/channel mode
final class ClientAPI
{
    private Connection $connection;
    private SessionInterface $session;

    public function __construct(Connection $connection, SessionInterface $session)
    {
        $this->connection = $connection;
        $this->session    = $session;
    }

    /**
     * Register in IRC network as a user
     *
     * @param string|null $pass
     * @param string $nick
     * @param string $user
     * @param string $realname
     * @param int $flags
     * @return Deferred
     */
    public function USER(?string $pass, string $nick, string $user, string $realname, int $flags): Deferred
    {
        if ($this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('Already registered');
        }

        $this->session->setFlag(SessionInterface::FLAG_REGISTERED);
        $this->session->setType(SessionInterface::TYPE_CLIENT);

        if (!empty($pass)) {
            $this->connection->send(new CMD(CMD::PASSWORD, [$pass]));
        }

        $this->connection->send(new CMD(CMD::NICK, [$nick]));
        $this->connection->send(new CMD(CMD::USER, [$user, $flags, '*'], $realname));

        return $this->connection->wait(RPL::WELCOME)->deferred();
    }

    /**
     * Register in IRC network as a service
     *
     * @param string|null $pass
     * @param string $name
     * @param string $servers
     * @param string $info
     * @return Deferred
     */
    public function SERVICE(?string $pass, string $name, string $servers, string $info): Deferred
    {
        if ($this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('Already registered');
        }

        $this->session->setFlag(SessionInterface::FLAG_REGISTERED);
        $this->session->setType(SessionInterface::TYPE_SERVICE);

        if (!empty($pass)) {
            $this->connection->send(new CMD(CMD::PASSWORD, [$pass]));
        }

        $this->connection->send(new CMD(CMD::SERVICE, [$name, 0, $servers, 0, 0], $info));

        return $this->connection->wait(RPL::YOU_ARE_SERVICE)->deferred();
    }

    /**
     * Change registered user nick
     *
     * @param string $nick
     * @return Deferred
     */
    public function NICK(string $nick): Deferred
    {
        if (!$this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('You must register before');
        }

        if ($this->session->getType() !== SessionInterface::TYPE_CLIENT) {
            throw new ProtocolException('You must register as client');
        }

        $this->connection->send(new CMD(CMD::NICK, [$nick]));
        return $this->connection->wait(CMD::NICK)->deferred();
    }

    /**
     * Login registered user as network operator
     *
     * @param string $name
     * @param string $password
     * @return Deferred
     */
    public function OPER(string $name, string $password): Deferred
    {
        if (!$this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('You must register before');
        }

        if ($this->session->getType() !== SessionInterface::TYPE_CLIENT) {
            throw new ProtocolException('You must register as client');
        }

        $this->connection->send(new CMD(CMD::OPERATOR, [$name, $password]));
        return $this->connection->wait(RPL::YOU_ARE_OPERATOR)->deferred();
    }

    /**
     * Quit IRC network with optional message
     *
     * @param string|null $message
     * @return Deferred
     */
    public function QUIT(string $message = null): Deferred
    {
        if (!$this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('You must register before');
        }

        $this->connection->send(new CMD(CMD::QUIT, [], $message));
        return $this->connection->wait(CMD::ERROR)->deferred();
    }

    /**
     * Get/Set user mode, unusable for services
     *
     * @param string $nick
     * @param string|null $mode
     * @return Deferred
     */
    public function userMODE(string $nick, string $mode = null): Deferred
    {
        if (!$this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('You must register before');
        }

        if ($this->session->getType() !== SessionInterface::TYPE_CLIENT) {
            throw new ProtocolException('You must register as client');
        }

        if (null !== $mode && !preg_match('/^[+\-][aioOrsw]$/', $mode)) {
            throw new ProtocolException('Invalid mode passed, allowed set only one per call');
        }

        $this->connection->send(new CMD(CMD::MODE, [$nick, $mode]));
        return $this->connection->wait(RPL::USER_MODE_IS)->deferred();
    }

    public function chanMODE(string $chan, string $mode = null, string $params = null): Deferred
    {
        if (!$this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('You must register before');
        }

        if ($this->session->getType() !== SessionInterface::TYPE_CLIENT) {
            throw new ProtocolException('You must register as client');
        }

        if (null !== $mode && !preg_match('/^[+\-][abeiIklmnoOpqrstv]$/', $mode)) {
            throw new ProtocolException('Invalid mode passed, allowed get/set only one per call');
        }

        $this->connection->send(new CMD(CMD::MODE, [$chan, $mode, $params]));
        if (null === $mode) {
            return $this->connection->wait(RPL::CHANNEL_MODE_IS)->deferred();
        }

        if (null === $params) {
            if ('+b' === $mode) {
                $this->connection->wait(RPL::BAN_LIST, RPL::END_OF_BAN_LIST);
            }

            if ('+e' === $mode) {
                $this->connection->wait(RPL::EXCEPTION_LIST, RPL::END_OF_EXCEPTION_LIST);
            }

            if ('+I' === $mode) {
                $this->connection->wait(RPL::INVITE_LIST, RPL::END_OF_INVITE_LIST);
            }
        }

        // Else wait for mode command
        return $this->connection->wait(CMD::MODE)->deferred();
    }

    // roles: REGISTERED
    public function AWAY(string $message = null): Deferred
    {
        $deferred = new Deferred();

        $this->connection->send(new CMD(CMD::AWAY, [], $message));
        $this->connection->wait(RPL::UN_AWAY, RPL::NOW_AWAY)
            ->deferred()
            ->then(fn($msg) => $deferred->resolved($msg))
            ->else(fn($err) => $deferred->rejected($err));

        return $deferred;
    }

    // roles: IRC_OPERATOR
    public function REHASH(): void
    {
        $this->connection->send(new CMD(CMD::REHASH));
        $this->connection->wait(RPL::REHASHING);
    }

    // roles: IRC_OPERATOR
    public function DIE(): void
    {
        $this->connection->send(new CMD(CMD::DIE));
        //TODO no response???
    }

    public function RESTART(): void
    {
        $this->connection->send(new CMD(CMD::RESTART));
        //TODO no response???
    }

    public function SUMMON(string $user, string $target = null, string $channel = null): void
    {
        //TODO can be disabled
        //TODO disable this possibility
        $this->connection->send(new CMD(CMD::SUMMON, [$user, $target, $channel]));
        $this->connection->wait(RPL::SUMMONING);
    }

    public function USERS(string $target = null): void
    {
        //TODO can be disabled
        //TODO used to list system users, no irc users
        $this->connection->send(new CMD(CMD::USERS, [$target]));
        $this->connection->wait([
            RPL::USERS_START,
            RPL::USERS,
            RPL::NO_USERS,
            RPL::END_OF_USERS,
        ]);
    }

    public function WALLOPS(string $message): void
    {
        $this->connection->send(new CMD(CMD::WALLOPS, [], $message));
        //TODO no reply???
    }

    public function USERHOST(string ...$nicknames): void
    {
        $this->connection->send(new CMD(CMD::USER_HOST, $nicknames));
        $this->connection->wait(RPL::USER_HOST);
    }

    public function ISON(string ...$nicknames): void
    {
        $this->connection->send(new CMD(CMD::IS_ON, $nicknames));
        $this->connection->wait(RPL::IS_ON);
    }

    public function WHO(string $mask, bool $operators = false): Deferred
    {
        if (!$this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('You must register before');
        }

        $this->connection->send(new CMD(CMD::WHO, [$mask, $operators ? 'o' : '']));
        $this->connection->wait(RPL::WHO_REPLY, RPL::END_OF_WHO);

        $deferred = new Deferred();
        $result   = [];
        $handler  = function (MSG $msg) use (&$handler, $deferred, &$result) {
            switch ($msg->getCode()) {
                case RPL::WHO_REPLY:
                    $result[] = $msg->getComment();
                    break;
                case RPL::END_OF_WHO:
                    $this->connection->detach(Connection::ON_INPUT, $handler);
                    $deferred->resolved($result);
                    break;
            }
        };

        $this->connection->attach(Connection::ON_INPUT, $handler);
        return $deferred;
    }

    /**
     * Query information about particularly user
     *
     * @param string|null $target
     * @param string ...$masks
     * @return Deferred
     * @codingStandardsIgnoreStart
     */
    public function WHO_IS(?string $target, string ...$masks): Deferred
    {
        if (!$this->session->hasFlag(SessionInterface::FLAG_REGISTERED)) {
            throw new ProtocolException('You must register before');
        }

        $this->connection->send(new CMD(CMD::WHO_IS, [$target, ...$masks]));
        $this->connection->wait(
            RPL::AWAY,
            RPL::WHO_IS_USER,
            RPL::WHO_IS_CHANNELS,
            RPL::WHO_IS_IDLE,
            RPL::WHO_IS_SERVER,
            RPL::WHO_IS_OPERATOR,
            RPL::WHO_IS_SECURE,
            RPL::WHO_IS_ACTUALLY,
            RPL::END_OF_WHO_IS,
        );

        $deferred = new Deferred();
        $result   = [];
        $handler  = function (MSG $msg) use (&$handler, $deferred, &$result) {
            switch ($msg->getCode()) {
                case RPL::AWAY:
                case RPL::WHO_IS_USER:
                case RPL::WHO_IS_CHANNELS:
                case RPL::WHO_IS_IDLE:
                case RPL::WHO_IS_SERVER:
                case RPL::WHO_IS_OPERATOR:
                case RPL::WHO_IS_SECURE:
                case RPL::WHO_IS_ACTUALLY:
                    $result[$msg->getCode()] = $msg->getComment();
                    break;
                case RPL::END_OF_WHO_IS:
                    $this->connection->detach(Connection::ON_INPUT, $handler);
                    $deferred->resolved($result);
                    break;
                case ERR::NO_SUCH_SERVER:
                case ERR::NO_SUCH_NICK:
                    $this->connection->detach(Connection::ON_INPUT, $handler);
                    $deferred->rejected($msg->getComment());
                    break;
            }
        };

        $this->connection->attach(Connection::ON_INPUT, $handler);
        return $deferred;
    }
}

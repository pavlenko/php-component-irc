<?php

namespace PE\Component\IRC\Client;

use PE\Component\IRC\CMD;
use PE\Component\IRC\Exception\ProtocolException;
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
    public function registerAsUser(?string $pass, string $nick, string $user, string $realname, int $flags): Deferred
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
    public function registerAsService(?string $pass, string $name, string $servers, string $info): Deferred
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

    //todo move mode, quit, squit here
    //TODO how to split mode commands

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
}

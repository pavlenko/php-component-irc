<?php

namespace PE\Component\IRC;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface as SocketConnection;
use React\Socket\SocketServer;

final class Server
{
    use HandleRegistrationCommands;
    use HandleUserCommands;
    use HandleChannelCommands;
    use HandleOperatorCommands;
    use HandleOtherCommands;

    private const COMMANDS = [
        //CMD::CMD_CAP         => [self::class, 'handleCAP'],
        CMD::CMD_ADMIN       => [self::class, 'handleADMIN'],
        CMD::CMD_AWAY        => [self::class, 'handleAWAY'],
        CMD::CMD_CONNECT     => [self::class, ''],
        CMD::CMD_ERROR       => [self::class, ''],
        CMD::CMD_INFO        => [self::class, 'handleINFO'],
        CMD::CMD_INVITE      => [self::class, 'handleINVITE'],
        CMD::CMD_IS_ON       => [self::class, 'handleISON'],
        CMD::CMD_JOIN        => [self::class, 'handleJOIN'],
        CMD::CMD_KICK        => [self::class, 'handleKICK'],
        CMD::CMD_KILL        => [self::class, 'handleKILL'],
        CMD::CMD_LINKS       => [self::class, ''],
        CMD::CMD_LIST        => [self::class, 'handleLIST'],
        CMD::CMD_MODE        => [self::class, 'handleMODE'],
        CMD::CMD_MOTD        => [self::class, 'handleMOTD'],
        CMD::CMD_LIST_USERS  => [self::class, ''],
        CMD::CMD_NAMES       => [self::class, 'handleNAMES'],
        CMD::CMD_NICK        => [self::class, 'handleNICK'],
        CMD::CMD_NOTICE      => [self::class, 'handleNOTICE'],
        CMD::CMD_OPERATOR    => [self::class, 'handleOPER'],
        CMD::CMD_PART        => [self::class, 'handlePART'],
        CMD::CMD_PASSWORD    => [self::class, 'handlePASS'],
        CMD::CMD_PING        => [self::class, 'handlePING'],
        CMD::CMD_PONG        => [self::class, 'handlePONG'],
        CMD::CMD_PRIVATE_MSG => [self::class, 'handlePRIVMSG'],
        CMD::CMD_QUIT        => [self::class, 'handleQUIT'],
        CMD::CMD_REHASH      => [self::class, 'handleREHASH'],
        CMD::CMD_RESTART     => [self::class, 'handleRESTART'],
        CMD::CMD_SERVER      => [self::class, ''],
        CMD::CMD_SERVER_QUIT => [self::class, ''],
        CMD::CMD_STATS       => [self::class, ''],
        CMD::CMD_SUMMON      => [self::class, ''],
        CMD::CMD_TIME        => [self::class, 'handleTIME'],
        CMD::CMD_TOPIC       => [self::class, 'handleTOPIC'],
        CMD::CMD_TRACE       => [self::class, ''],
        CMD::CMD_USER_HOST   => [self::class, 'handleUSERHOST'],
        CMD::CMD_USER        => [self::class, 'handleUSER'],
        CMD::CMD_USERS       => [self::class, ''],
        CMD::CMD_VERSION     => [self::class, 'handleVERSION'],
        CMD::CMD_WALLOPS     => [self::class, 'handleWALLOPS'],
        CMD::CMD_WHOIS       => [self::class, 'handleWHOIS'],
        CMD::CMD_WHO         => [self::class, 'handleWHO'],
        CMD::CMD_WHO_WAS     => [self::class, 'handleWHOWAS'],
    ];

    private Config $config;
    private History $history;
    private SessionMap $sessions;
    private ChannelMap $channels;

    /** @var array<string, string> */
    private array $operators = [];

    private ?SocketServer $socket = null;
    private LoopInterface $loop;
    private LoggerInterface $logger;

    public function __construct(Config $config, LoggerInterface $logger = null)
    {
        $this->config   = $config;//TODO config loader instead of config
        $this->history  = new History();
        $this->channels = new ChannelMap();
        $this->sessions = new SessionMap();

        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param string $address Port can be in range 6660–6669,7000
     * @param LoopInterface|null $loop
     * @return void
     */
    public function listen(string $address, LoopInterface $loop = null): void
    {
        $this->loop = $loop ?: Loop::get();
        $this->loop->addSignal(SIGINT, [$this, 'stop']);
        $this->loop->addSignal(SIGTERM, [$this, 'stop']);

        $this->loop->addPeriodicTimer($this->config->getMaxInactiveTimeout(), function () {
            foreach ($this->sessions as $user) {
                if (time() - $user->getLastMessageTime() > $this->config->getMaxInactiveTimeout()) {
                    $user->sendCMD(CMD::CMD_PING, [], null, $this->config->getName());
                    $user->updLastMessageTime();
                    $user->updLastPingingTime();
                    $user->setFlag(SessionInterface::FLAG_PINGING);
                }

                if (
                    $user->hasFlag(SessionInterface::FLAG_PINGING) &&
                    time() - $user->getLastPingingTime() > $this->config->getMaxInactiveTimeout()
                ) {
                    $user->close();
                }
            }
        });

        $this->socket = new SocketServer($address, [], $this->loop);
        $this->socket->on('connection', function (SocketConnection $connection) {
            $conn = new Connection($connection, $this->logger);
            $sess = new Session($conn, $this->config->getName(), parse_url($connection->getRemoteAddress(), PHP_URL_HOST));

            $this->sessions->attach($sess);

            $conn->attach(ConnectionInterface::EVT_INPUT, function (MSG $msg) use ($sess) {
                if (
                    !$sess->hasFlag(SessionInterface::FLAG_REGISTERED) &&
                    !in_array($msg->getCode(), [CMD::CMD_PASSWORD, CMD::CMD_NICK, CMD::CMD_USER, CMD::CMD_QUIT, CMD::CMD_CAP])
                ) {
                    $sess->sendERR(ERR::ERR_NOT_REGISTERED);
                } elseif (
                    array_key_exists($msg->getCode(), self::COMMANDS) &&
                    !empty(self::COMMANDS[$msg->getCode()][1])
                ) {
                    $this->{self::COMMANDS[$msg->getCode()][1]}($msg, $sess);
                }
                $sess->updLastMessageTime();
                //dump($this);
            });

            $conn->attach(ConnectionInterface::EVT_CLOSE, fn() => $this->sessions->detach($sess));
        });

        $this->logger->info('Listening on ' . $this->socket->getAddress());
    }

    public function stop(int $signal = null)
    {
        $this->logger->info('Stopping server ...');
        if (null !== $this->socket) {
            $this->socket->close();
        }
        if (null !== $signal) {
            $this->loop->removeSignal($signal, [$this, 'stop']);
        }
        $this->loop->stop();
        $this->logger->info('Stopping server OK');
    }

    private function isValidChannelName(string $name): bool
    {
        if (strlen($name) > 50) {
            $this->logger->debug('Session name must be less than 51 chars');
            return false;
        }
        if (!preg_match('/^[#@+!].+$/', $name)) {
            $this->logger->debug('Channel name must starts with "#", "@", "+" or "!"');
            return false;
        }
        if (!preg_match('/^[#@+!][\w\-\[\]\\\`^{}]+$/', $name)) {
            $this->logger->debug('Channel name contain invalid chars');
            return false;
        }

        return true;
    }

    private function isValidSessionName(string $name): bool
    {
        if (strlen($name) > 9) {
            $this->logger->debug('Session name must be less than 10 chars');
            return false;
        }
        if (preg_match('/^[0-9-].+$/', $name)) {
            $this->logger->debug('Session name must not starts with number or "-"');
            return false;
        }
        if (!preg_match('/^[\w\-\[\]\\\`^{}]+$/', $name)) {
            $this->logger->debug('Session name contain invalid chars');
            return false;
        }
        if ($this->config->getName() === $name) {
            $this->logger->debug('Session name must not equal server name');
            return false;
        }
        return true;
    }

    //TODO return false on error
    private function handleChannelFlags(CMD $cmd, SessionInterface $sess, ChannelInterface $chan)
    {
        $flag = $cmd->getArg(1);
        if ('o' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                $user = $this->sessions->searchByName($cmd->getArg(2));
                if (null === $user) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(2)]);
                } elseif ('+' === $flag[0]) {
                    $chan->operators()->attach($user);
                } elseif ('-' === $flag[0]) {
                    $chan->operators()->detach($user);
                }
            }
        } elseif ('p' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_PRIVATE);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_PRIVATE);
            }
        } elseif ('s' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_SECRET);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_SECRET);
            }
        } elseif ('i' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_INVITE_ONLY);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_INVITE_ONLY);
            }
        } elseif ('t' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_TOPIC_SET);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_TOPIC_SET);
            }
        } elseif ('m' === $flag[1]) {
            if ('+' === $flag[0]) {
                $chan->setFlag(ChannelInterface::FLAG_MODERATED);
            }
            if ('-' === $flag[0]) {
                $chan->clrFlag(ChannelInterface::FLAG_MODERATED);
            }
        } elseif ('l' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                if ('+' === $flag[0]) {
                    $chan->setLimit((int) $cmd->getArg(2));
                }
                if ('-' === $flag[0]) {
                    $chan->setLimit(0);
                }
            }
        } elseif ('k' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                if ('+' === $flag[0]) {
                    $chan->setPass($cmd->getArg(2));
                }
                if ('-' === $flag[0]) {
                    $chan->setPass('');
                }
            }
        } elseif ('b' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                if ('+' === $flag[0]) {
                    $masks = $this->channels->searchByName($cmd->getArg(0))->getBanMasks();
                    foreach ($masks as $mask) {
                        $sess->sendRPL(RPL::RPL_BAN_LIST, [$cmd->getArg(0), $mask]);
                    }
                    $sess->sendRPL(RPL::RPL_END_OF_BAN_LIST, [$cmd->getArg(0)]);
                } else {
                    $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
                }
            } elseif ('+' === $flag[0]) {
                $chan->addBanMask($cmd->getArg(2));
            } elseif ('-' === $flag[0]) {
                $chan->delBanMask($cmd->getArg(2));
            }
        } elseif ('v' === $flag[1]) {
            if ($cmd->numArgs() < 3) {
                $sess->sendERR(ERR::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]);
            } else {
                $user = $this->sessions->searchByName($cmd->getArg(2));
                if (null === $user) {
                    $sess->sendERR(ERR::ERR_NO_SUCH_NICK, [$cmd->getArg(2)]);
                } elseif ('+' === $flag[0]) {
                    $chan->speakers()->attach($user);
                } elseif ('-' === $flag[0]) {
                    $chan->speakers()->detach($user);
                }
            }
        } elseif ('n' !== $flag[1]) {
            $sess->sendERR(ERR::ERR_UNKNOWN_MODE, [$flag]);
        }
    }

    private function handleSessionFlags(CMD $cmd, SessionInterface $sess)
    {
        $flag = $cmd->getArg(1);
        if ('i' === $flag[1]) {
            if ('+' === $flag[0]) {
                $sess->setFlag(SessionInterface::FLAG_INVISIBLE);
            }
            if ('-' === $flag[0]) {
                $sess->clrFlag(SessionInterface::FLAG_INVISIBLE);
            }
        } elseif ('s' === $flag[1]) {
            if ('+' === $flag[0]) {
                $sess->setFlag(SessionInterface::FLAG_RECEIVE_NOTICE);
            }
            if ('-' === $flag[0]) {
                $sess->clrFlag(SessionInterface::FLAG_RECEIVE_NOTICE);
            }
        } elseif ('w' === $flag[1]) {
            if ('+' === $flag[0]) {
                $sess->setFlag(SessionInterface::FLAG_RECEIVE_WALLOPS);
            }
            if ('-' === $flag[0]) {
                $sess->clrFlag(SessionInterface::FLAG_RECEIVE_WALLOPS);
            }
        } elseif ('-o') {
            $sess->clrFlag(SessionInterface::FLAG_IS_OPERATOR);
        } else {
            $sess->sendERR(ERR::ERR_UNKNOWN_MODE, [$flag]);
        }
    }
}
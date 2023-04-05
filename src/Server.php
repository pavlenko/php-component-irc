<?php

namespace PE\Component\IRC;

use PE\Component\IRC\Handler\HandlerJOIN;
use PE\Component\IRC\Handler\HandlerNICK;
use PE\Component\IRC\Handler\HandlerOPER;
use PE\Component\IRC\Handler\HandlerPART;
use PE\Component\IRC\Handler\HandlerPASS;
use PE\Component\IRC\Handler\HandlerUSER;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Socket\ConnectionInterface as SocketConnection;
use React\Socket\SocketServer;

final class Server
{
    use HandleRegistrationCommands;
    use HandleUserCommands;
    use HandleChannelCommands;
    use HandleOperatorCommands;
    use HandleOtherCommands;

    private ConfigInterface $config;
    private EventsInterface $events;
    private LoggerInterface $logger;
    private LoopInterface $loop;

    private Storage $storage;
    private History $history;

    private ?SocketServer $socket = null;
    private ?TimerInterface $timer = null;

    private array $handlers;

    public function __construct(string $config, EventsInterface $events = null, LoggerInterface $logger = null, LoopInterface $loop = null)
    {
        $this->config = new Config($config);
        $this->config->load();

        $this->events = $events ?: new Events();
        $this->events->attach(Connection::EVT_INPUT, fn() => $this->onInput(...func_get_args()));

        $this->logger = $logger ?: new NullLogger();
        $this->loop   = $loop ?: Loop::get();

        $this->storage = new Storage($this->config, $this->events, $this->logger);
        $this->history = new History();

        $this->handlers = [
            //CMD::CMD_CAP         => [self::class, 'handleCAP'],
            CMD::CMD_ADMIN       => [$this, 'handleADMIN'],
            CMD::CMD_AWAY        => [$this, 'handleAWAY'],
            CMD::CMD_CONNECT     => [$this, ''],//TODO
            CMD::CMD_ERROR       => [$this, ''],//TODO
            CMD::CMD_INFO        => [$this, 'handleINFO'],
            CMD::CMD_INVITE      => [$this, 'handleINVITE'],
            CMD::CMD_IS_ON       => [$this, 'handleISON'],
            CMD::CMD_JOIN        => new HandlerJOIN(),
            CMD::CMD_KICK        => [$this, 'handleKICK'],
            CMD::CMD_KILL        => [$this, 'handleKILL'],
            CMD::CMD_LINKS       => [$this, ''],//TODO
            CMD::CMD_LIST        => [$this, 'handleLIST'],
            CMD::CMD_MODE        => [$this, 'handleMODE'],
            CMD::CMD_MOTD        => [$this, 'handleMOTD'],
            CMD::CMD_LIST_USERS  => [$this, ''],//TODO
            CMD::CMD_NAMES       => [$this, 'handleNAMES'],
            CMD::CMD_NICK        => new HandlerNICK(),
            CMD::CMD_NOTICE      => [$this, 'handleNOTICE'],
            CMD::CMD_OPERATOR    => new HandlerOPER(),
            CMD::CMD_PART        => new HandlerPART(),
            CMD::CMD_PASSWORD    => new HandlerPASS(),
            CMD::CMD_PING        => [$this, 'handlePING'],
            CMD::CMD_PONG        => [$this, 'handlePONG'],
            CMD::CMD_PRIVATE_MSG => [$this, 'handlePRIVMSG'],
            CMD::CMD_QUIT        => [$this, 'handleQUIT'],
            CMD::CMD_REHASH      => [$this, 'handleREHASH'],
            CMD::CMD_RESTART     => [$this, 'handleRESTART'],
            CMD::CMD_SERVER      => [$this, ''],//TODO
            CMD::CMD_SERVER_QUIT => [$this, ''],//TODO
            CMD::CMD_STATS       => [$this, ''],//TODO
            CMD::CMD_SUMMON      => [$this, ''],//TODO
            CMD::CMD_TIME        => [$this, 'handleTIME'],
            CMD::CMD_TOPIC       => [$this, 'handleTOPIC'],
            CMD::CMD_TRACE       => [$this, ''],//TODO
            CMD::CMD_USER_HOST   => [$this, 'handleUSERHOST'],
            CMD::CMD_USER        => new HandlerUSER(),
            CMD::CMD_USERS       => [$this, ''],//TODO
            CMD::CMD_VERSION     => [$this, 'handleVERSION'],
            CMD::CMD_WALLOPS     => [$this, 'handleWALLOPS'],
            CMD::CMD_WHOIS       => [$this, 'handleWHOIS'],
            CMD::CMD_WHO         => [$this, 'handleWHO'],
            CMD::CMD_WHO_WAS     => [$this, 'handleWHOWAS'],
        ];
    }

    private function onInput(MSG $msg, SessionInterface $sess)
    {
        if (
            !$sess->hasFlag(SessionInterface::FLAG_REGISTERED) &&
            !in_array($msg->getCode(), [CMD::CMD_PASSWORD, CMD::CMD_NICK, CMD::CMD_USER, CMD::CMD_QUIT, CMD::CMD_CAP])
        ) {
            $sess->sendERR(ERR::ERR_NOT_REGISTERED);
        } elseif (is_callable($this->handlers[$msg->getCode()] ?? null)) {
            call_user_func($this->handlers[$msg->getCode()], $msg, $sess, $this->storage);
        }
        $sess->updLastMessageTime();
        dump($this->storage);
    }

    public function config(string $key = null)
    {
        return $this->config->get($key);
    }

    public function listen(): void
    {
        $this->loop->addSignal(SIGINT, [$this, 'stop']);
        $this->loop->addSignal(SIGTERM, [$this, 'stop']);

        $this->timer = $this->loop->addPeriodicTimer($this->config(Config::CFG_MAX_INACTIVE_TIMEOUT), function () {
            foreach ($this->storage->sessions() as $user) {
                if (time() - $user->getLastMessageTime() > $this->config(Config::CFG_MAX_INACTIVE_TIMEOUT)) {
                    $user->sendCMD(CMD::CMD_PING, [], null, $this->config(Config::CFG_SERVER_NAME));
                    $user->updLastMessageTime();
                    $user->updLastPingingTime();
                    $user->setFlag(SessionInterface::FLAG_PINGING);
                }

                if (
                    $user->hasFlag(SessionInterface::FLAG_PINGING) &&
                    time() - $user->getLastPingingTime() > $this->config(Config::CFG_MAX_INACTIVE_TIMEOUT)
                ) {
                    $user->close();
                }
            }
        });

        $this->socket = new SocketServer($this->config(Config::CFG_SERVER_LISTEN), [], $this->loop);
        $this->socket->on('connection', function (SocketConnection $connection) {
            $conn = new Connection($connection, $this->logger);
            $sess = new Session(
                $conn,
                $this->config(Config::CFG_SERVER_NAME),
                parse_url($connection->getRemoteAddress(), PHP_URL_HOST)
            );

            $this->storage->sessions()->attach($sess);

            $conn->attach(Connection::EVT_INPUT, fn(MSG $msg) => $this->events->trigger(Connection::EVT_INPUT, $msg, $sess));

            /*$conn->attach(Connection::EVT_INPUT, function (MSG $msg) use ($sess) {
                if (
                    !$sess->hasFlag(SessionInterface::FLAG_REGISTERED) &&
                    !in_array($msg->getCode(), [CMD::CMD_PASSWORD, CMD::CMD_NICK, CMD::CMD_USER, CMD::CMD_QUIT, CMD::CMD_CAP])
                ) {
                    $sess->sendERR(ERR::ERR_NOT_REGISTERED);
                } elseif (is_callable($this->handlers[$msg->getCode()] ?? null)) {
                    call_user_func($this->handlers[$msg->getCode()], $msg, $sess, $this->storage);
                }
                $sess->updLastMessageTime();
                dump($this->storage);
            });*/

            $conn->attach(ConnectionInterface::EVT_CLOSE, fn() => $this->storage->sessions()->detach($sess));
        });

        $this->logger->info('Listening on ' . $this->socket->getAddress());
    }

    public function stop()
    {
        $this->logger->info('Stopping server ...');
        if (null !== $this->socket) {
            $this->socket->close();
        }
        if (null !== $this->timer) {
            $this->loop->cancelTimer($this->timer);
        }

        $this->loop->removeSignal(SIGINT, [$this, 'stop']);
        $this->loop->removeSignal(SIGTERM, [$this, 'stop']);
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
        if ($this->config(Config::CFG_SERVER_NAME) === $name) {
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
                $user = $this->storage->sessions()->searchByName($cmd->getArg(2));
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
                    $masks = $this->storage->channels()->searchByName($cmd->getArg(0))->getBanMasks();
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
                $user = $this->storage->sessions()->searchByName($cmd->getArg(2));
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
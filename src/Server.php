<?php

namespace PE\Component\IRC;

use PE\Component\IRC\Handler\HandlerJOIN;
use PE\Component\IRC\Handler\HandlerMODE;
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
            CMD::CMD_MODE        => new HandlerMODE(),
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
        //dump($msg->getCode(), $this->storage);
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
            $conn->attach(Connection::EVT_ERROR, fn() => $this->logger->error(func_get_arg(0)));
            $conn->attach(Connection::EVT_CLOSE, fn() => $this->storage->sessions()->detach($sess));
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
}
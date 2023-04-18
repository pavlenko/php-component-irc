<?php

namespace PE\Component\IRC;

use PE\Component\IRC\Handler\HandlerADMIN;
use PE\Component\IRC\Handler\HandlerAWAY;
use PE\Component\IRC\Handler\HandlerCONNECT;
use PE\Component\IRC\Handler\HandlerERROR;
use PE\Component\IRC\Handler\HandlerINFO;
use PE\Component\IRC\Handler\HandlerINVITE;
use PE\Component\IRC\Handler\HandlerISON;
use PE\Component\IRC\Handler\HandlerJOIN;
use PE\Component\IRC\Handler\HandlerKICK;
use PE\Component\IRC\Handler\HandlerKILL;
use PE\Component\IRC\Handler\HandlerLINKS;
use PE\Component\IRC\Handler\HandlerLIST;
use PE\Component\IRC\Handler\HandlerLUSERS;
use PE\Component\IRC\Handler\HandlerMODE;
use PE\Component\IRC\Handler\HandlerMOTD;
use PE\Component\IRC\Handler\HandlerNAMES;
use PE\Component\IRC\Handler\HandlerNICK;
use PE\Component\IRC\Handler\HandlerOPER;
use PE\Component\IRC\Handler\HandlerPART;
use PE\Component\IRC\Handler\HandlerPASS;
use PE\Component\IRC\Handler\HandlerPING;
use PE\Component\IRC\Handler\HandlerPONG;
use PE\Component\IRC\Handler\HandlerPRIVMSG;
use PE\Component\IRC\Handler\HandlerQUIT;
use PE\Component\IRC\Handler\HandlerREHASH;
use PE\Component\IRC\Handler\HandlerRESTART;
use PE\Component\IRC\Handler\HandlerSERVER;
use PE\Component\IRC\Handler\HandlerSQUIT;
use PE\Component\IRC\Handler\HandlerSTATS;
use PE\Component\IRC\Handler\HandlerSUMMON;
use PE\Component\IRC\Handler\HandlerTIME;
use PE\Component\IRC\Handler\HandlerTOPIC;
use PE\Component\IRC\Handler\HandlerUSER;
use PE\Component\IRC\Handler\HandlerUSERHOST;
use PE\Component\IRC\Handler\HandlerVERSION;
use PE\Component\IRC\Handler\HandlerWALLOPS;
use PE\Component\IRC\Handler\HandlerWHO;
use PE\Component\IRC\Handler\HandlerWHOIS;
use PE\Component\IRC\Handler\HandlerWHOWAS;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Socket\ConnectionInterface as SocketConnection;
use React\Socket\SocketServer;

final class Daemon
{
    public const EVT_REHASH  = 'rehash';
    public const EVT_RESTART = 'restart';

    private ConfigInterface $config;
    private EventsInterface $events;
    private LoggerInterface $logger;
    private LoopInterface $loop;

    private Storage $storage;

    private ?SocketServer $socket = null;
    private ?TimerInterface $timer = null;

    private array $handlers;

    public function __construct(
        string $config,
        EventsInterface $events = null,
        LoggerInterface $logger = null,
        LoopInterface $loop = null
    ) {
        $this->config = new Config($config);
        $this->config->load();

        $this->events = $events ?: new Events();
        $this->events->attach(Connection::EVT_INPUT, fn() => $this->onInput(...func_get_args()));
        $this->events->attach(self::EVT_REHASH, fn(Event $e) => $e->setPayload($this->config->load()));
        $this->events->attach(self::EVT_RESTART, fn() => $this->restart());

        $this->logger = $logger ?: new NullLogger();
        $this->loop   = $loop ?: Loop::get();

        $this->storage = new Storage($this->config, $this->events, $this->logger);

        $this->handlers = [
            //CMD::CMD_CAP         => [self::class, 'handleCAP'],
            CMD::CMD_ADMIN       => new HandlerADMIN(),
            CMD::CMD_AWAY        => new HandlerAWAY(),
            CMD::CMD_CONNECT     => new HandlerCONNECT(),
            CMD::CMD_ERROR       => new HandlerERROR(),
            CMD::CMD_INFO        => new HandlerINFO(),
            CMD::CMD_INVITE      => new HandlerINVITE(),
            CMD::CMD_IS_ON       => new HandlerISON(),
            CMD::CMD_JOIN        => new HandlerJOIN(),
            CMD::CMD_KICK        => new HandlerKICK(),
            CMD::CMD_KILL        => new HandlerKILL(),
            CMD::CMD_LINKS       => new HandlerLINKS(),
            CMD::CMD_LIST        => new HandlerLIST(),
            CMD::CMD_MODE        => new HandlerMODE(),
            CMD::CMD_MOTD        => new HandlerMOTD(),
            CMD::CMD_LIST_USERS  => new HandlerLUSERS(),
            CMD::CMD_NAMES       => new HandlerNAMES(),
            CMD::CMD_NICK        => new HandlerNICK(),
            CMD::CMD_NOTICE      => new HandlerPRIVMSG(),
            CMD::CMD_OPERATOR    => new HandlerOPER(),
            CMD::CMD_PART        => new HandlerPART(),
            CMD::CMD_PASSWORD    => new HandlerPASS(),
            CMD::CMD_PING        => new HandlerPING(),
            CMD::CMD_PONG        => new HandlerPONG(),
            CMD::CMD_PRIVATE_MSG => new HandlerPRIVMSG(),
            CMD::CMD_QUIT        => new HandlerQUIT(),
            CMD::CMD_REHASH      => new HandlerREHASH(),
            CMD::CMD_RESTART     => new HandlerRESTART(),
            CMD::CMD_SERVER      => new HandlerSERVER(),
            CMD::CMD_SERVER_QUIT => new HandlerSQUIT(),
            CMD::CMD_STATS       => new HandlerSTATS(),
            CMD::CMD_SUMMON      => new HandlerSUMMON(),
            CMD::CMD_TIME        => new HandlerTIME(),
            CMD::CMD_TOPIC       => new HandlerTOPIC(),
            CMD::CMD_TRACE       => [$this, ''],//TODO
            CMD::CMD_USER_HOST   => new HandlerUSERHOST(),
            CMD::CMD_USER        => new HandlerUSER(),
            CMD::CMD_USERS       => [$this, ''],//TODO
            CMD::CMD_VERSION     => new HandlerVERSION(),
            CMD::CMD_WALLOPS     => new HandlerWALLOPS(),
            CMD::CMD_WHOIS       => new HandlerWHOIS(),
            CMD::CMD_WHO         => new HandlerWHO(),
            CMD::CMD_WHO_WAS     => new HandlerWHOWAS(),
        ];
    }

    private function onInput(MSG $msg, SessionInterface $sess)
    {
        $allowed = [CMD::CMD_PASSWORD, CMD::CMD_NICK, CMD::CMD_USER, CMD::CMD_QUIT, CMD::CMD_CAP];
        if (!$sess->hasFlag(SessionInterface::FLAG_REGISTERED) && !in_array($msg->getCode(), $allowed)) {
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
                $overdue = time() - $user->getLastMessageTime() > $this->config(Config::CFG_MAX_INACTIVE_TIMEOUT);
                if ($overdue) {
                    $user->sendCMD(CMD::CMD_PING, [], null, $this->config(Config::CFG_SERVER_NAME));
                    $user->updLastMessageTime();
                    $user->updLastPingingTime();
                    $user->setFlag(SessionInterface::FLAG_PINGING);
                }

                $overdue = time() - $user->getLastPingingTime() > $this->config(Config::CFG_MAX_INACTIVE_TIMEOUT);
                if ($user->hasFlag(SessionInterface::FLAG_PINGING) && $overdue) {
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

            $conn->attach(Connection::EVT_INPUT, fn($m) => $this->events->trigger(Connection::EVT_INPUT, $m, $sess));
            $conn->attach(Connection::EVT_ERROR, fn() => $this->logger->error(func_get_arg(0)));
            $conn->attach(Connection::EVT_CLOSE, fn() => $this->storage->sessions()->detach($sess));
        });

        $this->logger->info('Listening on ' . $this->socket->getAddress());
    }

    public function restart()
    {
        foreach ($this->storage->sessions() as $user) {
            $user->close();
        }
        $this->stop();
        $this->config->load();
        $this->listen();
        $this->loop->run();
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

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
        CMD::CMD_CAP         => [self::class, 'handleCAP'],
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
    private EventsInterface $events;
    private LoggerInterface $logger;

    public function __construct(Config $config, EventsInterface $events = null, LoggerInterface $logger = null)
    {
        $this->config   = $config;//TODO config loader instead of config
        $this->history  = new History();
        $this->channels = new ChannelMap();
        $this->sessions = new SessionMap();

        $this->events = $events ?: new Events();
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
            $conn = new Connection($connection, $this->events, $this->logger);
            $sess = new Session($conn, $this->config->getName(), parse_url($connection->getRemoteAddress(), PHP_URL_HOST));

            $this->sessions->attach($sess);

            $this->events->attach(ConnectionInterface::EVT_INPUT, function (MSG $msg) use ($sess) {
                if (
                    !$sess->hasFlag(SessionInterface::FLAG_REGISTERED) &&
                    !in_array($msg->getCode(), [CMD::CMD_PASSWORD, CMD::CMD_NICK, CMD::CMD_USER, CMD::CMD_QUIT])
                ) {
                    $sess->sendERR(ERR::ERR_NOT_REGISTERED);
                } elseif (
                    array_key_exists($msg->getCode(), self::COMMANDS) &&
                    !empty(self::COMMANDS[$msg->getCode()][1])
                ) {
                    $this->{self::COMMANDS[$msg->getCode()][1]}($msg, $sess);
                }
                $sess->updLastMessageTime();
            });

            $this->events->attach(ConnectionInterface::EVT_CLOSE, fn() => $this->sessions->detach($sess));
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
}
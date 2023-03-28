<?php

namespace PE\Component\IRC;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface as SocketConnection;
use React\Socket\SocketServer;

class Server
{
    use HandleRegistrationCommands;
    use HandleUserCommands;
    use HandleChannelCommands;
    use HandleOperatorCommands;
    use HandleOtherCommands;

    private Config $config;
    private History $history;
    private SessionMap $sessions;
    private ChannelMap $channels;
    private array $operators = [];
    private ?string $capabilities = null;

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
     * @return void
     */
    public function listen(string $address, LoopInterface $loop = null): void
    {
        $this->loop = $loop ?: Loop::get();
        $this->loop->addSignal(SIGINT, [$this, 'stop']);
        $this->loop->addSignal(SIGTERM, [$this, 'stop']);

        $this->socket = new SocketServer($address, [], $this->loop);
        $this->socket->on('connection', function (SocketConnection $connection) {
            $this->logger->info('New connection from ' . $connection->getRemoteAddress());

            $conn = new Connection($connection, $this->events, $this->logger);
            $sess = new Session($conn, $this->config->getName(), $connection->getRemoteAddress());

            $this->sessions->attach($conn, $sess);

            $this->events->attach(ConnectionInterface::EVT_INPUT, function (MSG $msg) use ($conn, $sess) {
                //TODO on message received -> route to specific handle
            });
        });

        $this->logger->info('Listening on ' . $this->socket->getAddress());
    }

    private function processMessageReceived(SocketConnection $conn, Command $cmd)
    {
        $this->logger->info('<-- ' . $cmd);
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
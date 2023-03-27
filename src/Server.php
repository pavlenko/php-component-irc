<?php

namespace PE\Component\IRC;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

class Server
{
    use HandleRegistrationCommands;
    use HandleUserCommands;
    use HandleChannelCommands;
    use HandleOperatorCommands;
    use HandleOtherCommands;

    private Config $config;
    private Parser $parser;

    private History $history;
    private SessionMap $sessions;
    private ChannelMap $channels;
    private array $operators = [];
    private ?string $capabilities = null;

    private ?SocketServer $socket = null;
    private LoopInterface $loop;
    private LoggerInterface $logger;

    public function __construct(Config $config, LoopInterface $loop = null, LoggerInterface $logger = null)
    {
        $this->config   = $config;
        $this->parser   = new Parser();
        $this->history  = new History();
        $this->channels = new ChannelMap();
        $this->sessions = new SessionMap();

        $this->loop   = $loop ?: Loop::get();
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param string $address Port can be in range 6660–6669,7000
     * @return void
     */
    public function listen(string $address): void
    {
        $this->loop->addSignal(SIGINT, [$this, 'stop']);
        $this->loop->addSignal(SIGTERM, [$this, 'stop']);

        $this->socket = new SocketServer($address, [], $this->loop);
        $this->socket->on('connection', function (ConnectionInterface $connection) {
            $this->logger->info('New connection from ' . $connection->getRemoteAddress());

            //TODO maybe store connection in session and remove from map
            $conn = new Connection($connection, new Events(), $this->logger);
            $sess = new Session2($this->config->getName(), $connection->getRemoteAddress());

            $this->sessions->attach($conn, $sess);

            $connection->on('data', function ($data) use ($connection) {
                $lines = preg_split('/\r?\n/', $data, null, PREG_SPLIT_NO_EMPTY);
                dump($data);
                foreach ($lines as $line) {
                    $this->processMessageReceived($connection, $this->parser->parse($line));
                }
            });

            $connection->on('close', fn() => $this->logger->info('Close connection from ' . $connection->getRemoteAddress()));
        });

        $this->logger->info('Listening on ' . $this->socket->getAddress());
    }

    private function processMessageReceived(ConnectionInterface $conn, Command $cmd)
    {
        $this->logger->info('<-- ' . $cmd);

        $checkRegistration = function (Session $sess) {
            if (!$sess->capabilities && !empty($sess->nickname) && !empty($sess->username)) {
                if (empty($this->password) || $sess->password === $this->password) {
                    if (!($sess->flags & Session::REGISTERED)) {
                        $sess->flags |= Session::REGISTERED;

                        $sess->send(new Command(
                            Command::RPL_WELCOME,
                            [$sess->nickname],
                            "Welcome to the Internet Relay Network {$sess->nickname}"
                        ));
                        $sess->send(new Command(
                            Command::RPL_YOUR_HOST,
                            [$sess->nickname],
                            "Your host is {$this->config->getName()}, running version {$this->config->getVersionNumber()}"
                        ));
                        $sess->send(new Command(
                            Command::RPL_CREATED,
                            [$sess->nickname],
                            "This server was created {$this->config->getCreatedAt()->format('D M d Y H:i:s e')}"
                        ));
                        $sess->send(new Command(
                            Command::RPL_MY_INFO,
                            [
                                $sess->nickname,
                                $this->config->getName(),
                                $this->config->getVersionNumber(),
                                'DGMQRSZaghilopsuwz',
                                'CFILMPQRSTbcefgijklmnopqrstuvz',
                                'bkloveqjfI'
                            ]
                        ));
                    }
                } else {
                    $sess->quit();
                }
            }
        };

        $sess = $this->sessions[$conn];
        switch ($cmd->getCode()) {
            case Command::CMD_WHO:
                if (empty($cmd->getArgs())) {
                    $sess->send(new Command(Command::ERR_NEED_MORE_PARAMS, [$cmd->getCode()]));
                } else {
                    foreach ($this->sessions as $k) {
                        //TODO check equal by pattern
                        if (
                            $cmd->getArg(0) === $this->sessions[$k]->nickname &&
                            !($this->sessions[$k]->flags & Session::INVISIBLE)
                        ) {
                            $channel = '*';
                            $status  = '';

                            //TODO loop through user channels

                            if (
                                count($cmd->getArgs()) === 1 ||
                                $cmd->getArg(1) !== 'o' ||
                                ($cmd->getArg(1) === 'o' && $this->sessions[$k]->flags & Session::IRC_OPERATOR)
                            ) {
                                $sess->send(new Command(Command::RPL_WHO_REPLY, [
                                    $channel,
                                    $this->sessions[$k]->username,
                                    $this->sessions[$k]->hostname,
                                    $this->sessions[$k]->servername,
                                    $this->sessions[$k]->nickname,
                                    'H' . $status,
                                ], '0 ' . $this->sessions[$k]->realname));
                            }
                        }
                    }
                    $sess->send(new Command(Command::RPL_END_OF_WHO, [$this->config->getName()]));
                }
                break;
        }
    }

    public function processMessageSend(ConnectionInterface $conn, Command $cmd)
    {
        $this->logger->info('--> ' . $cmd);
        $conn->write($cmd . "\r\n");
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
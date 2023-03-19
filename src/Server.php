<?php

namespace PE\Component\IRC;

use PE\Component\IRC\Message\Replies;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;

class Server
{
    private string $name;
    private ?string $password;

    private Parser $parser;

    /**
     * @var \SplObjectStorage|Session[]
     */
    private \SplObjectStorage $sessions;

    private ?SocketServer $socket = null;
    private LoopInterface $loop;
    private LoggerInterface $logger;

    //TODO config file instead of $name, $password, $modt file
    public function __construct(string $name, string $pass = null, LoopInterface $loop = null, LoggerInterface $logger = null)
    {
        $this->name     = $name;
        $this->password = $pass;
        $this->parser   = new Parser();
        $this->sessions = new \SplObjectStorage();

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
            $this->sessions->attach($connection, new Session($connection, $this, ['addr' => $connection->getRemoteAddress()]));

            $connection->on('data', function ($data) use ($connection) {
                $lines = preg_split('/\r?\n/', $data, null, PREG_SPLIT_NO_EMPTY);
                dump($data);
                foreach ($lines as $line) {
                    $this->processMessageReceived($connection, $this->parser->parse($line));
                }
            });

            //TODO on close, on error
            $connection->on('close', fn() => $this->logger->info('Close connection from ' . $connection->getRemoteAddress()));
        });

        $this->logger->info('Listening on ' . $this->socket->getAddress());
    }

    private function processMessageReceived(ConnectionInterface $conn, Command $cmd)
    {
        $this->logger->info('<-- ' . $cmd);
        //$this->handler->handle($command, $this->sessions[$connection]);

        $checkRegistration = function (Session $sess) {
            if (!empty($sess->nickname) && !empty($sess->username)) {
                if (empty($this->password) || $sess->password === $this->password) {
                    if (!($sess->flags & Session::REGISTERED)) {
                        $sess->flags |= Session::REGISTERED;
                        //TODO send MODT
                    }
                } else {
                    $sess->quit();
                }
            }
        };

        $sess = $this->sessions[$conn];
        switch ($cmd->getName()) {
            case 'PASS':
                if (empty($cmd->getArg(0))) {
                    $sess->send(new Command(Replies::ERR_NEEDMOREPARAMS, [$cmd->getName()], 'Not enough parameters'));
                } elseif ($this->sessions[$conn]->flags & Session::REGISTERED) {
                    $sess->send(new Command(Replies::ERR_ALREADYREGISTRED, [$cmd->getName()], 'You may not re-register'));
                } else {
                    $sess->password = $cmd->getArg(0);
                }
                break;
            case 'NICK':
                if (empty($cmd->getArg(0))) {
                    $sess->send(new Command(Replies::ERR_NEEDMOREPARAMS, [$cmd->getName()], 'Not enough parameters'));
                } elseif (
                    strlen($cmd->getArg(0)) > 9 ||
                    !preg_match('/^[^0-9-][\w\-_\[\]\\\`^{}]{0,8}$/', $cmd->getArg(0)) ||
                    $this->name !== $cmd->getArg(0)
                ) {
                    $sess->send(new Command(Replies::ERR_ERRONEUSNICKNAME, [$cmd->getName()], 'Erroneous nickname'));
                } else {
                    // Check contain nick
                    foreach ($this->sessions as $k) {
                        if ($this->sessions[$k]->nickname === $cmd->getArg(0)) {
                            $sess->send(new Command(Replies::ERR_NICKNAMEINUSE, [$cmd->getName()], 'Nickname is already in use'));
                            break 2;
                        }
                    }
                    if ($this->sessions[$conn]->flags & Session::REGISTERED) {
                        //TODO Notify users (search in user channels) - check validity
                        //TODO format ":" + user.getPrefix() + " " + msg.getCommand() + " " + msg.getParams()[0] + "\n"
                        foreach ($this->sessions as $k) {
                            $this->sessions[$k]->send(new Command($cmd->getName(), [$cmd->getName()], null, $cmd->getArg(0)));
                        }
                    }
                    $sess->nickname = $cmd->getArg(0);
                }
                $checkRegistration($sess);
                break;
            case 'USER':
                if (count($cmd->getArgs()) < 4) {
                    $sess->send(new Command(Replies::ERR_NEEDMOREPARAMS, [$cmd->getName()], 'Not enough parameters'));
                } elseif ($this->sessions[$conn]->flags & Session::REGISTERED) {
                    $sess->send(new Command(Replies::ERR_ALREADYREGISTRED, [$cmd->getName()], 'You may not re-register'));
                } else {
                    $sess->username = $cmd->getArg(0);
                    $sess->realname = $cmd->getArg(3);
                }
                $checkRegistration($sess);
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
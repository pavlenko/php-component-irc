<?php

namespace PE\Component\IRC;

use PE\Component\IRC\Message\Replies;

class Handler
{
    private array $channels;

    /**
     * @param Channel[] $channels
     */
    public function __construct(array $channels)
    {
        $this->channels = $channels;
    }

    public function handle(Command $command, Session $session): void
    {
        //TODO need JOIN command for complete registration
        //TODO hierarchy: server->channel->topic
        //TODO maybe move to server class???
        switch ($command->getName()) {
            case 'NICK':
                $session->nick = $command->getParams()[0];
                break;
            case 'USER':
                $session->send(new Command(
                    null,
                    Replies::RPL_WELCOME,
                    [$command->getParams()[0]],
                    'Welcome to the Internet Relay Network ' . $session->nick
                ));
                break;
            case 'QUIT':
                $session->quit();
                break;
            case 'TIME':
                $session->send(new Command(
                    null,
                    Replies::RPL_TIME,
                    [parse_url($session->getConnection()->getLocalAddress(), PHP_URL_HOST)],
                    date(DATE_RFC3339)
                ));
                break;
            case 'JOIN':
                if (empty($command->getParams()[0])) {
                    $session->send(new Command(
                        null,
                        Replies::ERR_NOSUCHCHANNEL
                    ));
                } else {
                    $session->chan = $command->getParams()[0];
                }
                break;
            case 'LIST':
                $session->send(new Command(
                    null,
                    Replies::RPL_LISTSTART
                ));
                foreach ($this->channels as $channel) {
                    $session->send(new Command(
                        null,
                        Replies::RPL_LIST,
                        [$channel->getName(), 'NUMBER OF VISIBLE USERS'],
                        '[+' . $channel->getFlags() . '] ' . $channel->getTopic()
                    ));
                }
                $session->send(new Command(
                    null,
                    Replies::RPL_LISTEND,
                    [],
                    'End of /LIST'
                ));
                break;
            default:
                /*$session->send(new Command(
                    null,
                    Replies::ERR_UNKNOWNCOMMAND,
                    [$command->getName() ?: '""'],
                    'Unknown command'
                ));*/
        }
    }
}
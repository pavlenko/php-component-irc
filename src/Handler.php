<?php

namespace PE\Component\IRC;

use PE\Component\IRC\Message\Replies;
use React\Socket\ConnectionInterface;

class Handler
{
    public function handle(Command $command, Session $session): void
    {
        //TODO need JOIN command for complete registration
        //TODO hierarchy: server->channel->topic
        switch ($command->getName()) {
            case 'NICK':
                $session->nick = $command->getParams()[0];
                break;
            case 'USER':
                $session->send(new Command(
                    null,
                    Replies::RPL_WELCOME,
                    [$command->getParams()[0]],
                    sprintf(
                        'Welcome to the Internet Relay Network %s!%s@%s',
                        $session->nick,
                        $command->getParams()[0],
                        $session->addr
                    )
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
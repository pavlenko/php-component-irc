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
            case 'TIME':
                $session->send(new Command(
                    Replies::RPL_TIME,
                    [parse_url($session->getConnection()->getLocalAddress(), PHP_URL_HOST)],
                    date(DATE_RFC3339)
                ));
                break;
            case 'JOIN':
                if (empty($command->getArg(0))) {
                    $session->send(new Command(
                        Replies::ERR_NOSUCHCHANNEL
                    ));
                } else {
                    $session->chan = $command->getArgs()[0];
                }
                break;
            case 'LIST':
                $session->send(new Command(
                    Replies::RPL_LISTSTART
                ));
                foreach ($this->channels as $channel) {
                    $session->send(new Command(
                        Replies::RPL_LIST,
                        [$channel->getName(), /*'NUMBER OF VISIBLE USERS'*/count($channel->getUsers())],
                        '[+' . $channel->getFlags() . '] ' . $channel->getTopic()
                    ));
                }
                $session->send(new Command(
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
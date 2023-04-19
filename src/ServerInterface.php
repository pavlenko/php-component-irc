<?php

namespace PE\Component\IRC;

/**
 * @property $name
 * @property $pass
 * @property $id
 * @property $hop
 * @property $info
 */
interface ServerInterface
{
    //PASS <password> <version> <flags> [<options>]
    //SERVER <servername> <hopcount> <token> <info>
    //NICK <nickname> <hopcount> <username> <host> <servertoken> <umode> <realname>
    //SERVICE <servicename> <servertoken> <distribution> <type> <hopcount> <info>
    //QUIT [:<Quit Message>]
    //SQUIT <server> <comment>
    //NJOIN <channel> [ "@@" / "@" ] [ "+" ] <nickname> *( "," [ "@@" / "@" ] [ "+" ] <nickname> )
}

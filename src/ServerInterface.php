<?php

namespace PE\Component\IRC;

/**
 * Flags:
 * - Z: Enable compression on connection
 * - P: Enable Anti-abuse protection
 *
 * @property $name
 * @property $pass
 * @property $id
 * @property $hop
 * @property $info
 */
interface ServerInterface
{
    //REGISTRATION:
    //-->PASS <password> <version> <flags> [<options>]
    //-->SERVER <servername> <hopcount> <token> <info>
    //<--PASS from remote
    //<--SERVER from remote

    //this message form for inform other servers about new user
    //-->NICK <nickname> <hopcount> <username> <host> <servertoken> <umode> <realname>

    //this message form for inform other servers about new service
    //-->SERVICE <servicename> <servertoken> <distribution> <type> <hopcount> <info>

    //QUIT [:<Quit Message>]
    //SQUIT <server> <comment>
    //NJOIN <channel> [ "@@" / "@" ] [ "+" ] <nickname> *( "," [ "@@" / "@" ] [ "+" ] <nickname> )
}

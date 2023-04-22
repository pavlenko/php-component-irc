<?php

namespace PE\Component\IRC\Protocol;

/**
 * @property $password -- session
 * @property $nickname
 * @property $username
 * @property $realname
 * @property $hostname -- session
 *
 * @property $servername -- session
 *
 * @property $flags
 * @property $channels
 *
 * @property \DateTimeInterface $registeredAt -- session
 * @property \DateTimeInterface $lastPingingAt -- session
 * @property \DateTimeInterface $lastMessageAt -- session
 *
 * @property string $awayMessage
 * @property string $quitMessage
 */
interface ClientInterface
{
    //REGISTRATION: A
    //-->PASS <password>
    //-->NICK <nickname>
    //-->USER <user> <mode> <unused> <realname>
    //<--WELCOME

    //REGISTRATION: B
    //-->PASS <password>
    //-->SERVICE <nickname> <reserved> <distribution> <type> <reserved> <info>
    //<--YOURESERVICE
}

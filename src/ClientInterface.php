<?php

namespace PE\Component\IRC;

/**
 * @property $password
 * @property $nickname
 * @property $username
 * @property $realname
 * @property $hostname
 *
 * @property $servername
 *
 * @property $flags
 * @property $channels
 *
 * @property \DateTimeInterface $registeredAt
 * @property \DateTimeInterface $lastPingingAt
 * @property \DateTimeInterface $lastMessageAt
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

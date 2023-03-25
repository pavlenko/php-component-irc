<?php

namespace PE\Component\IRC;

final class HistoryItem
{
    private string $servername;
    private string $hostname;
    private string $nickname;
    private string $username;
    private string $realname;
    //TODO time

    public function __construct(SessionInterface $sess)
    {
        $this->servername = $sess->getServername();
        $this->hostname   = $sess->getHostname();
        $this->nickname   = $sess->getNickname();
        $this->username   = $sess->getUsername();
        $this->realname   = $sess->getRealname();
    }

    public function getServername(): string
    {
        return $this->servername;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRealname(): string
    {
        return $this->realname;
    }
}
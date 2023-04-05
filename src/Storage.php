<?php

namespace PE\Component\IRC;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Storage implements StorageInterface
{
    private ConfigInterface $config;
    private LoggerInterface $logger;
    private ChannelMap $channels;
    private SessionMap $sessions;

    public function __construct(ConfigInterface $config, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger ?: new NullLogger();

        $this->channels = new ChannelMap();
        $this->sessions = new SessionMap();
    }

    public function conf(string $name)
    {
        return $this->config->get($name);
    }

    public function channels(): ChannelMap
    {
        return $this->channels;
    }

    public function sessions(): SessionMap
    {
        return $this->sessions;
    }

    public function isValidChannelName(string $name): bool
    {
        if (strlen($name) > 50) {
            $this->logger->debug('Session name must be less than 51 chars');
            return false;
        }
        if (!preg_match('/^[#@+!].+$/', $name)) {
            $this->logger->debug('Channel name must starts with "#", "@", "+" or "!"');
            return false;
        }
        if (!preg_match('/^[#@+!][\w\-\[\]\\\`^{}]+$/', $name)) {
            $this->logger->debug('Channel name contain invalid chars');
            return false;
        }

        return true;
    }

    public function isValidSessionName(string $name): bool
    {
        if (strlen($name) > 9) {
            $this->logger->debug('Session name must be less than 10 chars');
            return false;
        }
        if (preg_match('/^[0-9-].+$/', $name)) {
            $this->logger->debug('Session name must not starts with number or "-"');
            return false;
        }
        if (!preg_match('/^[\w\-\[\]\\\`^{}]+$/', $name)) {
            $this->logger->debug('Session name contain invalid chars');
            return false;
        }
        if ($this->conf(Config::CFG_SERVER_NAME) === $name) {
            $this->logger->debug('Session name must not equal server name');
            return false;
        }
        return true;
    }
}
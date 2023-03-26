<?php

namespace PE\Component\IRC;

class Config
{
    private string $name;
    private \DateTimeInterface $createdAt;
    private string $version;
    private ?string $password;
    private ?string $modtFile;

    private string $info = '';

    private string $adminLocation1;// Typically: country, state and city; can be admin username
    private string $adminLocation2;// Typically: university and department; can be admin nickname
    private string $adminEmail;

    private int $maxChannels;
    private int $maxInactiveTimeout;
    private int $maxResponseTimeout;

    public function __construct(
        string $name,
        \DateTimeInterface $createdAt,
        string $version,
        string $password = null,
        string $modtFile = null,
        $adminLocation1 = '',
        $adminLocation2 = '',
        $adminEmail = '',
        int $maxChannels = 0,
        int $maxInactiveTimeout = 0,
        int $maxResponseTimeout = 0
) {
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->version = $version;
        $this->password = $password;
        $this->modtFile = $modtFile;

        $this->adminLocation1 = $adminLocation1;
        $this->adminLocation2 = $adminLocation2;
        $this->adminEmail     = $adminEmail;


        $this->maxChannels        = $maxChannels;
        $this->maxInactiveTimeout = $maxInactiveTimeout;
        $this->maxResponseTimeout = $maxResponseTimeout;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInfo(): string
    {
        return $this->info;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getMODT(): ?array
    {
        if (null !== $this->modtFile && is_readable($this->modtFile)) {
            return file($this->modtFile, FILE_IGNORE_NEW_LINES) ?: null;
        }
        return null;
    }

    public function getAdminLocation1(): string
    {
        return $this->adminLocation1;
    }

    public function getAdminLocation2(): string
    {
        return $this->adminLocation2;
    }

    public function getAdminEmail(): string
    {
        return $this->adminEmail;
    }

    public function getMaxChannels(): int
    {
        return $this->maxChannels;
    }

    public function getMaxInactiveTimeout(): int
    {
        return $this->maxInactiveTimeout;
    }

    public function getMaxResponseTimeout(): int
    {
        return $this->maxResponseTimeout;
    }
}
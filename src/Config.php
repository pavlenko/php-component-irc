<?php

namespace PE\Component\IRC;

/**
 * @deprecated
 */
final class Config
{
    public const DEFAULT_DATETIME_FORMAT  = 'D M d Y \a\t H:i:s e';//Thu Nov 10 2022 at 12:34:26 UTC
    public const DEFAULT_INACTIVE_TIMEOUT = 60;
    public const DEFAULT_RESPONSE_TIMEOUT = 10;

    private string $name;

    private string $adminLocation1;// Typically: country, state and city; can be admin username
    private string $adminLocation2;// Typically: university and department; can be admin nickname
    private string $adminEmail;

    private string $createdAt;

    private string $versionNumber;
    private string $versionDebug;
    private string $versionComment;

    private ?string $password;
    private ?string $motdFile;

    private string $info = '';

    private int $maxChannels;
    private int $maxInactiveTimeout;
    private int $maxResponseTimeout;

    public function __construct(
        string $name,
        string $adminLocation1,
        string $adminLocation2,
        string $adminEmail,
        string $versionNumber,
        string $versionDebug,
        string $versionComment,
        string $createdAt,
        string $password = null,
        string $modtFile = null,
        int $maxChannels = 0,
        int $maxInactiveTimeout = 0,
        int $maxResponseTimeout = 0
) {
        $this->name = $name;

        $this->adminLocation1 = $adminLocation1;
        $this->adminLocation2 = $adminLocation2;
        $this->adminEmail     = $adminEmail;

        $this->versionNumber  = $versionNumber;
        $this->versionDebug   = $versionDebug;
        $this->versionComment = $versionComment;

        $this->createdAt = $createdAt;
        $this->password = $password;
        $this->motdFile = $modtFile;

        $this->maxChannels        = $maxChannels;
        $this->maxInactiveTimeout = $maxInactiveTimeout ?: self::DEFAULT_INACTIVE_TIMEOUT;
        $this->maxResponseTimeout = $maxResponseTimeout ?: self::DEFAULT_RESPONSE_TIMEOUT;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getVersionNumber(): string
    {
        return $this->versionNumber;
    }

    public function getVersionDebug(): string
    {
        return $this->versionDebug;
    }

    public function getVersionComment(): string
    {
        return $this->versionComment;
    }

    public function getInfo(): string
    {
        return $this->info;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getMOTD(): ?array
    {
        if (null !== $this->motdFile && is_readable($this->motdFile)) {
            return file($this->motdFile, FILE_IGNORE_NEW_LINES) ?: null;
        }
        return null;
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
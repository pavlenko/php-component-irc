<?php

namespace PE\Component\IRC;

class Config
{
    private string $name;
    private \DateTimeInterface $createdAt;
    private string $version;
    private ?string $password;
    private ?string $modtFile;

    public function __construct(
        string $name,
        \DateTimeInterface $createdAt,
        string $version,
        string $password = null,
        string $modtFile = null
    ) {
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->version = $version;
        $this->password = $password;
        $this->modtFile = $modtFile;
    }

    public function getName(): string
    {
        return $this->name;
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
}
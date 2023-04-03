<?php

namespace PE\Component\IRC;

use Symfony\Component\Yaml\Yaml;

class Config2 implements ConfigInterface
{
    private string $path;
    private array $data = [];

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function load(): void
    {
        if (!is_readable($this->path)) {
            throw new \RuntimeException('Cannot load config file ' . $this->path);
        }
        $ext = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'php':
                $data = (array) require $this->path;
                break;
            case 'json':
                $data = (array) json_encode(file_get_contents($this->path));
                break;
            case 'yml':
                $data = (array) Yaml::parseFile($this->path);
                break;
            default:
                throw new \RuntimeException(
                    'Unsupported file type, allowed only .php, .json, .yml (require symfony/yaml package)'
                );
        }

        $missing = array_diff_key(self::CFG_REQUIRED, $data);
        if (!empty($missing)) {
            throw new \RuntimeException('Missing required config keys: ' . implode(',', $missing));
        }

        $this->data = [
            self::CFG_SERVERNAME           => $data[self::CFG_SERVERNAME],
            self::CFG_ADMIN_LOCATION1      => $data[self::CFG_ADMIN_LOCATION1],
            self::CFG_ADMIN_LOCATION2      => $data[self::CFG_ADMIN_LOCATION2],
            self::CFG_ADMIN_EMAIL          => $data[self::CFG_ADMIN_EMAIL],
            self::CFG_CREATED_AT           => $data[self::CFG_CREATED_AT],
            self::CFG_VERSION_NUMBER       => $data[self::CFG_VERSION_NUMBER],
            self::CFG_VERSION_DEBUG        => $data[self::CFG_VERSION_DEBUG],
            self::CFG_VERSION_COMMENT      => $data[self::CFG_VERSION_COMMENT] ?? null,
            self::CFG_PASSWORD             => $data[self::CFG_PASSWORD] ?? null,
            self::CFG_MOTD_FILE            => $data[self::CFG_MOTD_FILE] ?? null,
            self::CFG_INFO                 => $data[self::CFG_INFO],
            self::CFG_MAX_CHANNELS         => $data[self::CFG_MAX_CHANNELS] ?? 0,
            self::CFG_MAX_INACTIVE_TIMEOUT => $data[self::CFG_MAX_INACTIVE_TIMEOUT] ?? self::DEFAULT_INACTIVE_TIMEOUT,
            self::CFG_MAX_RESPONSE_TIMEOUT => $data[self::CFG_MAX_RESPONSE_TIMEOUT] ?? self::DEFAULT_RESPONSE_TIMEOUT,
            self::CFG_OPERATORS            => array_filter((array) ($data[self::CFG_OPERATORS] ?? [])),
        ];
    }

    /**
     * @param string|null $key
     * @return string|int|array
     */
    public function get(string $key = null)
    {
        if (null === $key) {
            return $this->data;
        }
        if (!array_key_exists($key, $this->data)) {
            throw new \InvalidArgumentException('Unknown config key');
        }
        return $this->data[$key];
    }
}
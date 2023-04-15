<?php

namespace PE\Component\IRC;

interface ConfigInterface
{
    public const DEFAULT_DATETIME_FORMAT  = 'D M d Y \a\t H:i:s e';//Thu Nov 10 2022 at 12:34:26 UTC
    public const DEFAULT_INACTIVE_TIMEOUT = 60;
    public const DEFAULT_RESPONSE_TIMEOUT = 10;

    public const CFG_SERVER_LISTEN        = 'listen';// Port can be in range 6660–6669,7000
    public const CFG_SERVER_NAME          = 'name';
    public const CFG_ADMIN_LOCATION1      = 'admin_location1';
    public const CFG_ADMIN_LOCATION2      = 'admin_location2';
    public const CFG_ADMIN_EMAIL          = 'admin_email';
    public const CFG_CREATED_AT           = 'created_at';
    public const CFG_VERSION_NUMBER       = 'version_number';
    public const CFG_VERSION_DEBUG        = 'version_debug';
    public const CFG_VERSION_COMMENT      = 'version_comment';
    public const CFG_PASSWORD             = 'password';
    public const CFG_MOTD_FILE            = 'motd_file';
    public const CFG_INFO                 = 'info';
    public const CFG_MAX_CHANNELS         = 'max_channels';
    public const CFG_MAX_INACTIVE_TIMEOUT = 'max_inactive_timeout';
    public const CFG_MAX_RESPONSE_TIMEOUT = 'max_response_timeout';
    public const CFG_OPERATORS            = 'operators';

    public const CFG_REQUIRED = [
        self::CFG_SERVER_NAME,
        self::CFG_ADMIN_LOCATION1,
        self::CFG_ADMIN_LOCATION2,
        self::CFG_ADMIN_EMAIL,
        self::CFG_CREATED_AT,
        self::CFG_VERSION_NUMBER,
        self::CFG_VERSION_DEBUG,
        self::CFG_INFO,
    ];

    public function __construct(string $path);
    public function load(): string;
    public function get(string $key = null);
}
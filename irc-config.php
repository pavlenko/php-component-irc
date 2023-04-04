<?php

use PE\Component\IRC\Config;

return [
    Config::CFG_SERVER_NAME     => 'local.dev',
    Config::CFG_ADMIN_LOCATION1 => 'test',
    Config::CFG_ADMIN_LOCATION2 => 'test',
    Config::CFG_ADMIN_EMAIL     => 'test@local.dev',
    Config::CFG_VERSION_NUMBER  => 'v1.0',
    Config::CFG_VERSION_DEBUG   => 'dev',
    Config::CFG_VERSION_COMMENT => 'testing',
    Config::CFG_CREATED_AT      => date(Config::DEFAULT_DATETIME_FORMAT),
    Config::CFG_PASSWORD        => null,
    Config::CFG_MOTD_FILE       => __DIR__ . '/IRCat.motd',
    Config::CFG_INFO            => '',
    Config::CFG_OPERATORS       => ['master' => hash('sha256', 'master')]
];
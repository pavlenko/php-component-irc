<?php

namespace PE\Component\IRC\Client;

class ChannelsAPI
{
    // roles: REGISTERED
    public function JOIN(): void
    {
    }

    // roles: REGISTERED
    public function PART(): void
    {
    }

    // roles: REGISTERED|CHANNEL_OPERATOR
    public function MODE(): void
    {
    }

    // roles: REGISTERED|CHANNEL_OPERATOR
    public function TOPIC(): void
    {
    }

    // roles: REGISTERED
    public function NAMES(): void
    {
    }

    // roles: REGISTERED
    public function LIST(): void
    {
    }

    // roles: REGISTERED|CHANNEL_OPERATOR
    public function INVITE(): void
    {
    }

    // roles: REGISTERED|CHANNEL_OPERATOR
    public function KICK(): void
    {
    }
}

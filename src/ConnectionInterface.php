<?php

namespace PE\Component\IRC;

interface ConnectionInterface
{
    public const EVT_ERROR = 'conn.error';
    public const EVT_CLOSE = 'conn.close';
    public const EVT_INPUT = 'conn.input';

    public function write(MSG $msg): bool;
    public function close(): void;
}
<?php

namespace PE\Component\IRC\Protocol;

interface StorageInterface
{
    //commands - collect call count & payload bytes in tx/rx
    //Command(name, executions, tx_bytes)

    //sessions - all sessions (registered and stale)
    //channels - channels state
}

<?php

namespace PE\Component\IRC\Protocol;

interface StorageInterface
{
    //commands - collect call count & payload bytes in tx/rx
    //sessions - all sessions (registered and stale) - add role param
    //- client
    //--- user
    //--- service
    //- server
    //channels - channels state
}

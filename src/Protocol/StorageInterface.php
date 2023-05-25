<?php

namespace PE\Component\IRC\Protocol;

/**
 * @property CommandInterface[] $commands
 * @property ClientInterface[] $clients
 * @property RoleServiceInterface[] $services
 */
interface StorageInterface
{
    //TODO create dto models for each state part, try to not use collections on use only them as typed collection
    //commands - collect call count & payload bytes in tx/rx
    //sessions - all sessions (registered and stale) - add role param
    //- client
    //--- user
    //--- service
    //- server
    //channels - channels state
    //history - log client activity history, maybe also remote server

    /**
     * TODO try use doc as below for valid code navigation
     * @template T
     *
     * @param string $request
     * @param T $target
     * @return T
     */
    public function template(string $request, $target);
}

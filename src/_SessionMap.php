<?php

namespace PE\Component\IRC;

//TODO $key = spl_object_hash($conn)
//TODO $val = [$conn, $sess]
class _SessionMap
{
    public function attach(Connection $conn, SessionInterface $sess){}
    public function detach(Connection $conn){}
    public function containsName(string $name){}
}
<?php

namespace PE\Component\IRC;

class Parser
{
    public function parse(string $data): Command
    {
        $parts = preg_split('/\s+/', $data, 2, PREG_SPLIT_NO_EMPTY);

        // Resolve prefix
        $prefix = null;
        if (!empty($parts) && ':' === $parts[0][0]) {
            $prefix = substr($parts[0], 1);
            $data   = $parts[1] ?? '';
        }

        // Resolve command
        $parts   = preg_split('/\s+/', $data, 2, PREG_SPLIT_NO_EMPTY);
        $command = strtoupper(array_shift($parts) ?? '');//TODO maybe throw exception here

        // Resolve comment & params
        $parts   = preg_split('/:/', $parts[0] ?? '', 2, PREG_SPLIT_NO_EMPTY);
        $params  = preg_split('/\s+/', $parts[0] ?? '', null, PREG_SPLIT_NO_EMPTY);
        $comment = !empty($parts[1]) ? trim($parts[1]) : null;

        return new Command($command, $params, $comment, $prefix);
    }
}
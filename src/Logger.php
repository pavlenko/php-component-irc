<?php

namespace PE\Component\IRC;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

final class Logger extends AbstractLogger
{
    public const VERBOSITY_QUIET        = 1;
    public const VERBOSITY_NORMAL       = 2;
    public const VERBOSITY_VERBOSE      = 4;
    public const VERBOSITY_VERY_VERBOSE = 8;
    public const VERBOSITY_DEBUG        = 16;

    private const VERBOSITY_MAP = [
        LogLevel::EMERGENCY => self::VERBOSITY_NORMAL,
        LogLevel::ALERT     => self::VERBOSITY_NORMAL,
        LogLevel::CRITICAL  => self::VERBOSITY_NORMAL,
        LogLevel::ERROR     => self::VERBOSITY_NORMAL,
        LogLevel::WARNING   => self::VERBOSITY_NORMAL,
        LogLevel::NOTICE    => self::VERBOSITY_VERBOSE,
        LogLevel::INFO      => self::VERBOSITY_VERY_VERBOSE,
        LogLevel::DEBUG     => self::VERBOSITY_DEBUG,
    ];

    private const OUTPUT_MAP = [
        LogLevel::EMERGENCY => STDERR,
        LogLevel::ALERT     => STDERR,
        LogLevel::CRITICAL  => STDERR,
        LogLevel::ERROR     => STDERR,
        LogLevel::WARNING   => STDOUT,
        LogLevel::NOTICE    => STDOUT,
        LogLevel::INFO      => STDOUT,
        LogLevel::DEBUG     => STDOUT,
    ];

    // format (0m - means reset style): \033[<style>m<string>\033[0m
    // style can be combined via ";" separator
    private const STYLES_MAP = [
        LogLevel::EMERGENCY => "\033[" . self::BG_RED . "m[emr] %s\033[0m",
        LogLevel::ALERT     => "\033[" . self::BG_RED . "m[alr] %s\033[0m",
        LogLevel::CRITICAL  => "\033[" . self::BG_RED . "m[cri] %s\033[0m",
        LogLevel::ERROR     => "\033[" . self::FG_RED . "m[e] %s\033[0m",
        LogLevel::WARNING   => "\033[" . self::FG_YELLOW . "m[w] %s\033[0m",
        LogLevel::NOTICE    => "\033[" . self::FG_GREEN . "m[n] %s\033[0m",
        LogLevel::INFO      => "\033[" . self::FG_CYAN . "m[i] %s\033[0m",
        LogLevel::DEBUG     => "\033[" . self::FG_GREY . "m[d] %s\033[0m",
    ];

    public const FG_BLACK  = 30;
    public const FG_RED    = 31;
    public const FG_GREEN  = 32;
    public const FG_YELLOW = 33;
    public const FG_BLUE   = 34;
    public const FG_PURPLE = 35;
    public const FG_CYAN   = 36;
    public const FG_GREY   = 37;

    public const BG_BLACK  = 40;
    public const BG_RED    = 41;
    public const BG_GREEN  = 42;
    public const BG_YELLOW = 43;
    public const BG_BLUE   = 44;
    public const BG_PURPLE = 45;
    public const BG_CYAN   = 46;
    public const BG_GREY   = 47;

    public const ST_RESET       = 0;
    public const ST_NORMAL      = 0;
    public const ST_BOLD        = 1;
    public const ST_ITALIC      = 3;
    public const ST_UNDERLINE   = 4;
    public const ST_BLINK       = 5;
    public const ST_NEGATIVE    = 7;
    public const ST_CONCEALED   = 8;
    public const ST_CROSSED_OUT = 9;
    public const ST_FRAMED      = 51;
    public const ST_ENCIRCLED   = 52;
    public const ST_OVERLINE    = 53;

    private int $verbosity;

    public function __construct(int $verbosity = self::VERBOSITY_NORMAL)
    {
        if ($verbosity < self::VERBOSITY_QUIET) {
            throw new \InvalidArgumentException();
        }

        $this->verbosity = $verbosity;
    }

    public function log($level, $message, array $context = []): void
    {
        if (!array_key_exists($level, self::OUTPUT_MAP)) {
            $level = LogLevel::NOTICE;
        }

        if (self::VERBOSITY_MAP[$level] > $this->verbosity) {
            return;
        }

        fwrite(self::OUTPUT_MAP[$level], sprintf(self::STYLES_MAP[$level], $message) . "\n");
    }
}
<?php

namespace Jabirchall\Supermassive\Commands;

abstract class BaseCommand
{
    public const ARGUMENT_COUNT = 0;

    public abstract function encode(): string;

    public function __toString(): string
    {
        return $this->encode();
    }
}
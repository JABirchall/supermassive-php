<?php

namespace Jabirchall\Supermassive\Commands;

class Authenticate extends BaseCommand
{
    public const ARGUMENT_COUNT = 2;

    private string $username;
    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function encode(): string
    {
        return base64_encode($this->username . '\0' . $this->password);
    }
}
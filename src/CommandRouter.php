<?php

namespace Jabirchall\Supermassive;

use InvalidArgumentException;
use Jabirchall\Supermassive\Commands\Authenticate;
use Jabirchall\Supermassive\Exceptions\ConnectionException;

class CommandRouter
{
    /*
     * Array of supported commands mapped to their respective classes
     */
    private static array $commands = [
        'authenticate' => Authenticate::class,
        'PUT'  => '',
        'GET'  => '',
        'DEL'  => '',
        'INCR' => '',
        'DECR' => '',
        'REGX' => '',
    ];

    private Connection $connection;

    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }
    
    /**
     * @param string $command
     * @return string
     */
    public function getCommandClass(string $command): string
    {
        return self::$commands[$command];
    }

    /**
     * @param string $command
     * @return bool
     */
    public function isSupported(string $command): bool
    {
        return array_key_exists($command, self::$commands);
    }

    /**
     * Excutes a command from supported command classes
     *
     * Supported commands: AUTH PUT GET DEL INCR DECR REGX
     *
     * @throws ConnectionException
     */
    public function executeCommand(string $command, array $args): string
    {
        $commandClass = $this->getCommandClass($command);

        if($commandClass::ARGUMENT_COUNT > sizeof($args)) {
            throw new InvalidArgumentException("Command [$command] expects at least " . $commandClass::ARGUMENT_COUNT . " arguments");
        }

        $command = new ($this->getCommandClass($command))(...$args);
        $this->connection->sendCommand($command->encode());
        return $this->connection->readResponse();
    }
}
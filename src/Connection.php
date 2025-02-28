<?php

namespace Jabirchall\Supermassive;


use Jabirchall\Supermassive\Exceptions\AuthenticationException;
use Jabirchall\Supermassive\Exceptions\ConnectionException;
use Jabirchall\Supermassive\Exceptions\NotSupportedException;
use Socket;

/**
 * Class Connection
 *
 * This class is responsible for handling the connection to the database
 */
class Connection
{
    /**
     * The socket connection instance
     */
    private Socket|false $socket;

    /**
     * Connect to the database
     *
     * @throws ConnectionException
     */
    public function connect(string $host, int $port): void
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->socket === false || $this->socket instanceof Socket) {
            $errorCode = socket_last_error();
            socket_clear_error();
            throw new ConnectionException("Failed to create socket: " . socket_strerror($errorCode), $errorCode);
        }

        socket_connect($this->socket, $host, $port);
    }

    public function disconnect(): void
    {
        socket_close($this->socket);
        socket_clear_error();
    }

    /**
     * @throws ConnectionException
     */
    public function sendCommand(string $command): void
    {
        $success = socket_write($this->socket, $command, mb_strlen($command));
        if ($success === false) {
            $errorCode = socket_last_error();
            socket_clear_error();
            throw new ConnectionException('Failed to send command: '. socket_strerror($errorCode), $errorCode);
        }
    }


    /**
     * @throws ConnectionException
     */
    public function readResponse(): string
    {
        $response = socket_read($this->socket, PHP_INT_MAX, PHP_NORMAL_READ);
        if ($response === false) {
            $errorCode = socket_last_error();
            socket_clear_error();
            throw new ConnectionException('Failed to read response: '. socket_strerror($errorCode), $errorCode);
        }
        return $response;
    }

    /**
     * Checks if the socket extension is loaded
     *
     * @throws NotSupportedException
     */
    public function hasSocket(): bool
    {
        if (!extension_loaded('sockets')) {
            throw new NotSupportedException('The "sockets" php extension is required for this connection.');
        }

        return true;
    }
}
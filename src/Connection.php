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
 *
 */
class Connection
{
    /**
     * The socker connection instance
     *
     * @var false|Socket
     */
    private $socket;

    /**
     * Connect to the database
     *
     * @throws ConnectionException
     */
    public function connect(string $host, int $port)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->socket === false || $this->socket instanceof Socket) {
            $errorCode = socket_last_error();
            socket_clear_error();
            throw new ConnectionException("Create socket failed: " . socket_strerror($errorCode), $errorCode);
        }

        socket_connect($this->socket, $host, $port);
    }

    /**
     * @throws ConnectionException|AuthenticationException
     */
    public function authenticate(string $auth)
    {
        $this->sendCommand($auth);
        $response = $this->readResponse();
        if ($response !== 'OK authenticated') {
            throw new AuthenticationException('Connection failed: Invalid credentials', 401);
        }
    }

    public function disconnect()
    {
        socket_close($this->socket);
        socket_clear_error();
    }

    /**
     * @throws ConnectionException
     */
    public function sendCommand(string $command)
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
    public function readResponse()
    {
        $response = socket_read($this->socket, 1024);
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
    public function hasSocket()
    {
        if (!extension_loaded('sockets')) {
            throw new NotSupportedException('The "sockets" php extension is required for this connection.');
        }
    }
}
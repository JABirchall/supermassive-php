<?php

namespace Jabirchall\Supermassive;

use Jabirchall\Supermassive\Exceptions\AuthenticationException;
use Jabirchall\Supermassive\Exceptions\ConnectionException;
use Jabirchall\Supermassive\Exceptions\NotSupportedException;

/**
 * Class Client
 *
 * @method string Authenticate(string $username, string $password)
 */
class Client
{
    /**
     * The connection address to the database
     * 127.0.0.1
     */
    private string $host;

    /**
     * The username to connect to the database
     * admin
     */
    private string $username;

    /**
     * The password to connect to the database
     *
     * P4s$w0Rd
     */
    private string $password;

    /**
     * The connection instance
     */
    private Connection $connection;

    /**
     * The port to connect to the database
     * 4000
     */
    private int $port;

    private CommandRouter $commandRouter;

    /**
     * Create a Connection instance
     *
     * <code>
     *      use Jabirchall\Supermassive\Client;
     *      $host = '127.0.0.1'
     *      $port = 4000
     *      $username = 'admin'
     *      $password = 'P4s$w0Rd'
     *      $supermassive = new Client($host, $port, $username, $password)
     * </code>
     */
    public function __construct(
        string $host,
        int $port,
        string $username,
        string $password
    )
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->commandRouter = new CommandRouter;
        $this->connection = new Connection;
    }

    /**
     * Connect to the database
     *
     * @throws ConnectionException|AuthenticationException|NotSupportedException
     *
     * <code>
     *     use Jabirchall\Supermassive\Client;
     *     $supermassive = new Client("127.0.0.1", 4000, "admin", "P4s$w0Rd");
     *     $supermassive->connect();
     * </code>
     */
    public function connect(): void
    {
        if(empty($this->username) || empty($this->password)) {
            throw new AuthenticationException('Username and password are required to connect to a supermassive database');
        }

        $this->connection->hasSocket();
        $this->connection->connect($this->host, $this->port);
        $this->commandRouter->setConnection($this->connection);

        $response = $this->Authenticate($this->username, $this->password);
        if ($response !== 'OK authenticated') {
            throw new AuthenticationException('Connection failed: Invalid credentials', 401);
        }
    }

    public function disconnect(): void
    {
        $this->connection->disconnect();
    }

    /**
     * @throws NotSupportedException|ConnectionException
     */
    public function __call($commandID, $arguments): string
    {
        if (!$this->commandRouter->isSupported($commandID)) {
            throw new NotSupportedException("Command [$commandID] is not supported");
        }

        return $this->commandRouter->executeCommand($commandID, $arguments);
    }
}
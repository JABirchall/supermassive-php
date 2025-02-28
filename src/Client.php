<?php

namespace Jabirchall\Supermassive;

use Jabirchall\Supermassive\Exceptions\AuthenticationException;
use Jabirchall\Supermassive\Exceptions\ConnectionException;
use Jabirchall\Supermassive\Exceptions\NotSupportedException;

/**
 * Class Client
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
    private int $port = 4000;

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
        string $host = "127.0.0.1",
        int $port = 4000,
        string $username,
        string $password
    )
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
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
    public function connect()
    {
        if(empty($this->username) || empty($this->password)) {
            throw new AuthenticationException('Username and password are required to connect to a supermassive database');
        }

        $this->connection = new Connection;
        $this->connection->hasSocket();
        $this->connection->connect($this->host, $this->port);
        $this->connection->authenticate(base64_encode($this->username . '\n' . $this->password));
    }

    public function disconnect()
    {
        $this->connection->disconnect();
    }
}
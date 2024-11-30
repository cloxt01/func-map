<?php

namespace App\Src;

abstract class db
{
    protected string $host;
    protected int $port;
    protected string $username;
    protected string $password;
    protected mixed $connection;

    public function __construct()
    {
        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
        $configFile = __DIR__ . '/../../../../../config/db.conf';
        if (!file_exists($configFile)) {
            throw new \RuntimeException("Configuration file not found: {$configFile}");
        }

        $configLines = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $config = [];
        foreach ($configLines as $line) {
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value);
        }

        $this->host = $config['host'] ?? 'localhost';
        $this->port = isset($config['port']) ? (int)$config['port'] : 3306;
        $this->username = $config['username'] ?? 'root';
        $this->password = $config['password'] ?? '';
    }

    abstract protected function getConnection(): mixed;

    abstract protected function closeConnection(): bool;
}
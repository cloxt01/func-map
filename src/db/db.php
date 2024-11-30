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
        try {
            $configFile = __DIR__ . '/../../../../../conf/db.conf';
            $configPath = realpath($configFile);
            if (!file_exists($configFile)) {
                throw new \RuntimeException("Configuration file not found: {$configPath}");
            }

            $configLines = file($configFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            $config = [];
            foreach ($configLines as $line) {
                list($key, $value) = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }

            if (!$configPath || empty($config)) {
                throw new \RuntimeException("Configuration file is empty: {$configPath}");
            } else if (!isset($config['host']) || !isset($config['port']) || !isset($config['username']) || !isset($config['password'])) {
                throw new \RuntimeException("Configuration file is incomplete: {$configPath}");
            }

            $this->host = $config['host'];
            $this->port = $config['port'];
            $this->username = $config['username'];
            $this->password = $config['password'];

        } catch (\RuntimeException $e) {
            echo($e->getMessage().PHP_EOL);
            exit(255);
        }
    }

    abstract protected function getConnection(): mixed;

    abstract protected function closeConnection(): bool;
}

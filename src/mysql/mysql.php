<?php

namespace App\Src;

class mysql extends db
{
    private string $dbname;

    public function __construct(string $dbname)
    {
        parent::__construct();
        $this->dbname = $dbname;
    }

    public function getConnection(): \PDO|null
    {
        try {

            $this->connection = new \PDO("mysql:host={$this->host};port={$this->port};dbname={$this->dbname}", $this->username, $this->password);

            if (!is_null($this->connection)) {
                echo("Connection running on [http://{$this->host}:{$this->port}]".PHP_EOL);
                echo("Database: {$this->dbname}".PHP_EOL);
                return $this->connection;
            } else {
                throw new \PDOException("Connecting failed", 400);
            }
        } catch (\PDOException|\Exception |\RuntimeException $e) {
            echo($e->getMessage().PHP_EOL);
            return null;
        }
    }
    public function closeConnection(): bool
    {
        try {
            if ($this->connection !== null) {
                $this->connection = null;
                echo("Connection closed");
                return true;
            } else {
                throw new \PDOException("Connection isn't found", 404);
            }
        } catch (\PDOException|\Exception $e) {
            echo($e->getMessage().PHP_EOL);
            return false;
        }
    }
}
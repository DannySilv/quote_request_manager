<?php

namespace App\Db;

use PDO;
use PDOException;
use RuntimeException;

class DatabaseConnection 
{
    public function __construct(private array $configs) {}
    
    public function connect(): PDO
    {
        $databaseConfig = $this->configs['database'];

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $databaseConfig['host'],
            $databaseConfig['port'],
            $databaseConfig['name'],
            $databaseConfig['charset']
        );

        try {
            return new PDO(
                $dsn,
                $databaseConfig['user'],
                $databaseConfig['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }
}
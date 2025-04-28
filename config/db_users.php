<?php
class DatabaseUsers {
    private static $connection = null;
    private static $config = [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'db_users',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    ];
    public static function getConnection() {
        if (self::$connection === null) {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                self::$config['host'],
                self::$config['dbname'],
                self::$config['charset']
            );
            self::$connection = new PDO(
                $dsn,
                self::$config['username'],
                self::$config['password'],
                self::$config['options']
            );
        }
        return self::$connection;
    }
    public static function closeConnection() {
        self::$connection = null;
    }
} 
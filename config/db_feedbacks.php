<?php
/**
 * Kelas DatabaseFeedbacks untuk mengelola koneksi database feedbacks
 */
class DatabaseFeedbacks {
    private static $connection = null;
    private static $config = [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'db_feedbacks',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    ];

    /**
     * Mendapatkan koneksi database feedbacks
     * @return PDO Object koneksi database
     * @throws Exception Jika koneksi gagal
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
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
            } catch (PDOException $e) {
                error_log("Database Feedbacks Connection Error: " . $e->getMessage());
                throw new Exception("Terjadi kesalahan saat menghubungkan ke database feedbacks. Silakan coba lagi nanti.");
            }
        }
        return self::$connection;
    }

    /**
     * Menutup koneksi database
     */
    public static function closeConnection() {
        self::$connection = null;
    }
} 
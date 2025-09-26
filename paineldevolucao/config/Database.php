<?php
class Database {
    private static $host = '127.0.0.1';
    private static $db   = 'sqlpaineldevolucao';
    private static $user = 'root';
    private static $pass = '';
    private static $charset = 'utf8mb4';
    private static $pdo;

    public static function getConnection() {
        if (!self::$pdo) {
            $dsn = "mysql:host=".self::$host.";dbname=".self::$db.";charset=".self::$charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            self::$pdo = new PDO($dsn, self::$user, self::$pass, $options);
        }
        return self::$pdo;
    }
}

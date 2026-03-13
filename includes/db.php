<?php
require_once __DIR__ . '/../config/config.php';

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:40px;text-align:center;color:#c00;">
                <h2>Database Connection Error</h2>
                <p>Unable to connect to the database. Please check your configuration.</p>
                <small>' . htmlspecialchars($e->getMessage()) . '</small>
            </div>');
        }
    }
    return $pdo;
}

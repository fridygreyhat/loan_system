<?php
require_once __DIR__ . '/auth.php';

$db_url = getenv('DATABASE_URL');

if ($db_url) {
    $parsed = parse_url($db_url);
    $hostname = $parsed['host'];
    $username = $parsed['user'];
    $password = $parsed['pass'];
    $database = ltrim($parsed['path'], '/');
    $port = isset($parsed['port']) ? $parsed['port'] : 5432;
} else {
    $hostname = "localhost";
    $username = "postgres";
    $password = "";
    $database = "loandisk";
    $port = 5432;
}

try {
    $dsn = "pgsql:host=$hostname;port=$port;dbname=$database";
    $con = new PDO($dsn, $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection Failed: ' . $e->getMessage());
}

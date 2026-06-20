<?php
// db_config.php

// .envファイルを読み込む関数
function loadEnv($path) {
    if (!file_exists($path)) return;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// .envの読み込み（プロジェクトのrootに.envがある）
loadEnv(__DIR__ . '/../.env');

// 変数に代入
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? '';
$db_user = $_ENV['DB_USER'] ?? '';
$db_pass = $_ENV['DB_PASS'] ?? '';

$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
$dsn_init = 'mysql:host={$db_host};charset=utf8mb4';

// (オプション) ここでPDO接続までやってしまうとさらに楽です
// try {
//     $pdo = new PDO($dsn, $db_user, $db_pass, [
//         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//     ]);
// } catch (PDOException $e) {
//     exit('DB接続失敗: ' . $e->getMessage());
// }

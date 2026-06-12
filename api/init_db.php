<?php
header('Content-Type: text/html; charset=utf-8');

// XAMPPのMySQL（根本）への接続設定
$dsn_init = 'mysql:host=localhost;charset=utf8mb4';
$db_user = 'root';
$db_pass = '';

try {
    $db = new PDO($dsn_init, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. データベースがなければ自動作成
    $db->exec("CREATE DATABASE IF NOT EXISTS gameportal CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
    $db->exec("USE gameportal;");

    // 2. ユーザーテーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sql);

    // 3. テストユーザー(admin)の登録
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES ('admin', :hash)");
        $stmt->execute([':hash' => $hash]);
        echo "【大成功】本物のMySQLにデータベースとテーブルを作成し、テストユーザー(admin)を登録しました！";
    } else {
        echo "【確認】MySQLデータベースはすでに初期化されています。";
    }
} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
}

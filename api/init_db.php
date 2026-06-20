<?php
header('Content-Type: text/html; charset=utf-8');

// 共通ファイルを読み込む（これだけで $pdo や各変数が使えるようになります）
require_once __DIR__ . '/db_config.php';

// 2. 安全のために英数字とアンダースコア以外を削除（セキュリティ対策）
$safe_db_name = preg_replace('/[^a-zA-Z0-9_]/', '', $db_name);

try {
    $db = new PDO($dsn_init, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. データベースがなければ自動作成
    $db->exec("CREATE DATABASE IF NOT EXISTS `{$safe_db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
    $db->exec("USE `{$safe_db_name}`;");

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

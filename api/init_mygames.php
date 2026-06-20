<?php
header('Content-Type: text/html; charset=utf-8');

// 共通ファイルを読み込む（これだけで $pdo や各変数が使えるようになります）
require_once __DIR__ . '/db_config.php';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. user_games テーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS user_games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        thumbnail VARCHAR(255) NOT NULL,
        play_time DECIMAL(5,1) NOT NULL,
        last_played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sql);

    // 2. テストデータ（ユーザーID: 1：admin用）の登録（データが空の場合のみ）
    $stmt = $db->prepare("SELECT COUNT(*) FROM user_games WHERE user_id = 1");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        $insert = $db->prepare("INSERT INTO user_games (user_id, title, thumbnail, play_time) VALUES (:uid, :title, :thumb, :pt)");
        
        $insert->execute([
            ':uid' => 1,
            ':title' => 'レイヤード・ストーリー (MySQL版)',
            ':thumb' => 'https://placehold.jp/200x200.jpg',
            ':pt' => 24.5
        ]);

        $insert->execute([
            ':uid' => 1,
            ':title' => 'シンギュラリティ・コード (MySQL版)',
            ':thumb' => 'https://placehold.jp/200x200.jpg',
            ':pt' => 8.2
        ]);
        
        echo "【大成功】user_gamesテーブルを作成し、MySQLにプレイデータを登録しました！";
    } else {
        echo "【確認】user_gamesテーブルはすでに初期化されています。";
    }
} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
}

<?php
header('Content-Type: text/html; charset=utf-8');

// 共通ファイルを読み込む（これだけで $pdo や各変数が使えるようになります）
require_once __DIR__ . '/db_config.php';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. games テーブルの作成
    $db->exec("CREATE TABLE IF NOT EXISTS home_games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(10) NOT NULL,
        title VARCHAR(100) NOT NULL,
        thumbnail VARCHAR(255) NOT NULL,
        price INT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. home_rankings テーブルの作成
    $db->exec("CREATE TABLE IF NOT EXISTS home_rankings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rank_type VARCHAR(10) NOT NULL,
        rank_num INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        thumbnail VARCHAR(255) NOT NULL,
        score DECIMAL(2,1) NOT NULL,
        price VARCHAR(20) NOT NULL,
        change_num INT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 3. ゲームデータの初期投入（データが空の場合のみ）
    $count = $db->query("SELECT COUNT(*) FROM home_games")->fetchColumn();
    if ($count == 0) {
        $stmt = $db->prepare("INSERT INTO home_games (category, title, thumbnail, price) VALUES (:cat, :title, :thumb, :price)");
        
        // カテゴリAとBにそれぞれ12件ずつ（計24件）ダミーを生成（無限スクロール検証用）
        foreach (['A', 'B'] as $cat) {
            for ($i = 1; $i <= 12; $i++) {
                $stmt->execute([
                    ':cat' => $cat,
                    ':title' => "【MySQL】ADVタイトル {$cat}-#{$i}",
                    ':thumb' => "https://placehold.jp/200x120.jpg?text={$cat}-{$i}",
                    ':price' => rand(1500, 5000)
                ]);
            }
        }
    }

    // 4. ランキングデータの初期投入（データが空の場合のみ）
    $rcount = $db->query("SELECT COUNT(*) FROM home_rankings")->fetchColumn();
    if ($rcount == 0) {
        $stmt = $db->prepare("INSERT INTO home_rankings (rank_type, rank_num, title, thumbnail, score, price, change_num) VALUES (:type, :num, :title, :thumb, :score, :price, :change)");
        
        $types = ['A' => '人気急上昇', 'B' => '高評価', 'C' => '売上トップ'];
        foreach ($types as $key => $name) {
            for ($r = 1; $r <= 5; $r++) {
                $stmt->execute([
                    ':type' => $key,
                    ':num' => $r,
                    ':title' => "【MySQL/解禁】{$name} 第{$r}位",
                    ':thumb' => "https://placehold.jp/800x480.jpg?text={$key}-{$r}",
                    ':score' => number_format(5.0 - ($r * 0.1), 1),
                    ':price' => $r == 3 ? '無料' : '¥' . rand(2000, 4000),
                    ':change' => rand(-2, 2)
                ]);
            }
        }
    }

    echo "【大成功】ホーム画面用のゲーム一覧＆ランキングデータをMySQLに登録しました！";

} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
}

<?php
header('Content-Type: text/html; charset=utf-8');

// 共通ファイルを読み込む（これだけで $pdo や各変数が使えるようになります）
require_once __DIR__ . '/db_config.php';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. 各種サブテーブルの作成
    // ゲームの複数画像を保存するテーブル
    $db->exec("CREATE TABLE IF NOT EXISTS game_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT NOT NULL,
        image_url VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 実績テーブル
    $db->exec("CREATE TABLE IF NOT EXISTS game_achievements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT NOT NULL,
        icon VARCHAR(10) NOT NULL,
        title VARCHAR(50) NOT NULL,
        `desc` VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // DLCテーブル
    $db->exec("CREATE TABLE IF NOT EXISTS game_dlcs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        price VARCHAR(20) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // お知らせテーブル
    $db->exec("CREATE TABLE IF NOT EXISTS game_news (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT NOT NULL,
        content VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. テストデータ（今回はすでに存在するhome_gamesのID: 1のゲーム用）のデータ投入（空の場合のみ）
    $imgCount = $db->query("SELECT COUNT(*) FROM game_images WHERE game_id = 1")->fetchColumn();
    if ($imgCount == 0) {
        // 画像データの投入
        $imgStmt = $db->prepare("INSERT INTO game_images (game_id, image_url) VALUES (1, :url)");
        $imgStmt->execute([':url' => 'https://placehold.jp/1200x675.jpg']);
        $imgStmt->execute([':url' => 'https://placehold.jp/1200x675.jpg']);
        $imgStmt->execute([':url' => 'https://placehold.jp/1200x675.jpg']);

        // 実績データの投入
        $achStmt = $db->prepare("INSERT INTO game_achievements (game_id, icon, title, `desc`) VALUES (1, :icon, :title, :desc)");
        $achStmt->execute([':icon' => '🏆', ':title' => 'MySQLの試練突破', ':desc' => '本物のデータベース接続に成功した']);
        $achStmt->execute([':icon' => '💾', ':title' => 'データモデリング', ':desc' => 'リレーショナルテーブルを構築した']);

        // DLCデータの投入
        $dlcStmt = $db->prepare("INSERT INTO game_dlcs (game_id, title, price) VALUES (1, :title, :price)");
        $dlcStmt->execute([':title' => '【MySQL限定配信】追加外伝シナリオ', ':price' => '¥1,500']);

        // お知らせデータの投入
        $newsStmt = $db->prepare("INSERT INTO game_news (game_id, content) VALUES (1, :content)");
        $newsStmt->execute([':content' => '【重要】MySQL連携による詳細データ配信を開始しました。']);
    }

    echo "【大成功】詳細画面用（画像・実績・DLC・おしらせ）のRDB子テーブル群をMySQLに作成・登録しました！";

} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
}

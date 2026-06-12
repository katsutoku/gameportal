<?php
header('Content-Type: application/json; charset=utf-8');

$gameId = isset($_GET['id']) ? (int)$_GET['id'] : 1; // デフォルトは1

$dsn = 'mysql:host=localhost;dbname=gameportal;charset=utf8mb4';
$db_user = 'root';
$db_pass = '';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. 親テーブル (home_games) から基本情報を取得
    $stmt = $db->prepare("SELECT title, category FROM home_games WHERE id = :id");
    $stmt->execute([':id' => $gameId]);
    $gameBase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$gameBase) {
        echo json_encode(["error" => "ゲームが見つかりません。"]);
        exit;
    }

    // 2. 各種子テーブルからデータを個別に取得 (1対多のセオリー通りの安全な実装)
    $imgStmt = $db->prepare("SELECT image_url FROM game_images WHERE game_id = :id");
    $imgStmt->execute([':id' => $gameId]);
    $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN); // URLの配列として取得

    $achStmt = $db->prepare("SELECT icon, title, `desc` FROM game_achievements WHERE game_id = :id");
    $achStmt->execute([':id' => $gameId]);
    $achievements = $achStmt->fetchAll(PDO::FETCH_ASSOC);

    $dlcStmt = $db->prepare("SELECT title, price FROM game_dlcs WHERE game_id = :id");
    $dlcStmt->execute([':id' => $gameId]);
    $dlcs = $dlcStmt->fetchAll(PDO::FETCH_ASSOC);

    $newsStmt = $db->prepare("SELECT content FROM game_news WHERE game_id = :id");
    $newsStmt->execute([':id' => $gameId]);
    $news = $newsStmt->fetchAll(PDO::FETCH_COLUMN);

    // 3. すべてのデータを統合したレスポンスJSONの作成
    echo json_encode([
        "title" => $gameBase['title'],
        "category" => $gameBase['category'] . " (MySQL連動)",
        "release_date" => "2026年6月12日",
        "description" => "この詳細情報は、MySQLデータベースの複数の関連テーブルからリレーションシップに基づいて動的に生成されています。バニラJSのfetchにより、ページの部分更新が実現されています。",
        "images" => $images,
        "achievements" => $achievements,
        "dlcs" => $dlcs,
        "news" => $news
    ]);

} catch (PDOException $e) {
    echo json_encode(["error" => "DBエラーが発生しました。"]);
}

<?php
header('Content-Type: application/json; charset=utf-8');

$category = isset($_GET['category']) ? $_GET['category'] : 'A';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 5;
$offset = ($page - 1) * $limit;

// 共通ファイルを読み込む（これだけで $pdo や各変数が使えるようになります）
require_once __DIR__ . '/db_config.php';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 指定されたカテゴリのデータをLIMIT/OFFSETで取得
    $stmt = $db->prepare("SELECT id, title, thumbnail, price FROM home_games WHERE category = :cat LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':cat', $category, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 次のページがあるか件数チェック
    $nextOffset = $page * $limit;
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM home_games WHERE category = :cat");
    $checkStmt->execute([':cat' => $category]);
    $total = $checkStmt->fetchColumn();
    $hasMore = $nextOffset < $total;

    echo json_encode([
        "page" => $page,
        "hasMore" => $hasMore,
        "games" => $games
    ]);

} catch (PDOException $e) {
    echo json_encode(["page" => $page, "hasMore" => false, "games" => []]);
}

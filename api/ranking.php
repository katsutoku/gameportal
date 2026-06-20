<?php
header('Content-Type: application/json; charset=utf-8');

$type = isset($_GET['type']) ? $_GET['type'] : 'A';

// 共通ファイルを読み込む（これだけで $pdo や各変数が使えるようになります）
require_once __DIR__ . '/db_config.php';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ランキングデータの取得
    $stmt = $db->prepare("SELECT rank_num AS rank, title, thumbnail, score, price, change_num AS `change` FROM home_rankings WHERE rank_type = :type ORDER BY rank_num ASC");
    $stmt->execute([':type' => $type]);
    $rankingData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rankingData);

} catch (PDOException $e) {
    echo json_encode([]);
}

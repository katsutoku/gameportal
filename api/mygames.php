<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. セッションチェック
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "authenticated" => false,
        "games" => []
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// 共通ファイルを読み込む（これだけで $pdo や各変数が使えるようになります）
require_once __DIR__ . '/db_config.php';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. ログイン中のユーザーIDに一致するゲームデータをMySQLから取得
    $stmt = $db->prepare("SELECT title, thumbnail, play_time FROM user_games WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $myGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. 結果を返却
    echo json_encode([
        "authenticated" => true,
        "username" => $_SESSION['username'],
        "games" => $myGames
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "authenticated" => true,
        "games" => [],
        "error" => "DBエラーが発生しました。"
    ]);
}

<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$username = isset($input['username']) ? trim($input['username']) : '';
$password = isset($input['password']) ? trim($input['password']) : '';

if (empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "ユーザー名とパスワードを入力してください。"]);
    exit;
}

$db_file = __DIR__ . '/users_db.json';

if (!file_exists($db_file)) {
    echo json_encode(["success" => false, "message" => "データベースが初期化されていません。"]);
    exit;
}

try {
    // 1. ファイル（DB）から全ユーザーのデータを読み込む
    $users = json_decode(file_get_contents($db_file), true);

    // 2. SQLの 「WHERE username = :username」 を配列検索で完全再現
    $matched_user = null;
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            $matched_user = $user;
            break;
        }
    }

    // 3. パスワードの安全な照合（本物のDB実装と全く同じ暗号化ロジック）
    if ($matched_user && password_verify($password, $matched_user['password_hash'])) {
        // ログイン成功：セッションに保存
        $_SESSION['user_id'] = $matched_user['id'];
        $_SESSION['username'] = $matched_user['username'];
        
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "ユーザー名またはパスワードが正しくありません。"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "データ処理エラーが発生しました。"]);
}

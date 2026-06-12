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

// 本物のMySQLへの接続設定
$dsn = 'mysql:host=localhost;dbname=gameportal;charset=utf8mb4';
$db_user = 'root';
$db_pass = '';

try {
    $db = new PDO($dsn, $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQLインジェクションを防ぐ安全なクエリ実行
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $matched_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // パスワードの安全な照合
    if ($matched_user && password_verify($password, $matched_user['password_hash'])) {
        // ログイン成功：セッションに保存
        $_SESSION['user_id'] = $matched_user['id'];
        $_SESSION['username'] = $matched_user['username'];
        
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "ユーザー名またはパスワードが正しくありません。"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "データ処理エラーが発生しました。"]);
}

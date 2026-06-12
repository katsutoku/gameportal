<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// セッション変数を全て解除
$_SESSION = array();

// クッキーのセッションIDも削除
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 最終的にセッションを破棄
session_destroy();

echo json_encode(["success" => true]);

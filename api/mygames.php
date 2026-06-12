<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. セッションチェック（ログイン状態の確認）
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "authenticated" => false,
        "games" => []
    ]);
    exit;
}

// 2. 本来はここで「ユーザーID」を元にDBからデータを引きますが、一先ず連想配列（模擬データ）で用意します
// ※ adminユーザー（ID: 1）の所持ゲームデータ
$myGamesData = [
    [
        "id" => 101,
        "title" => "レイヤード・ストーリー",
        "thumbnail" => "https://placeholder.com",
        "play_time" => 12.5
    ],
    [
        "id" => 102,
        "title" => "シンギュラリティ・コード",
        "thumbnail" => "https://placeholder.com",
        "play_time" => 4.2
    ]
];

// 3. フロントが求める形（JSON）で返却
echo json_encode([
    "authenticated" => true,
    "username" => $_SESSION['username'],
    "games" => $myGamesData
]);

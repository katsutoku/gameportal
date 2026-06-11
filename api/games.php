<?php
// ブラウザにJSONとして返すためのヘッダー設定
header('Content-Type: application/json; charset=utf-8');

// フロントから送られてきたパラメータを取得（デフォルト値を設定）
$category = isset($_GET['category']) ? $_GET['category'] : 'A';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 最大3ページまでデータが存在することにする（テスト用）
$maxPages = 3;
$hasMore = $page < $maxPages;

$games = [];

// 1ページあたり5個のダミーデータを生成
if ($page <= $maxPages) {
    for ($i = 1; $i <= 5; $i++) {
        $id = (($page - 1) * 5) + $i;
        $games[] = [
            "id" => $id,
            "title" => "【カテゴリ{$category}】ADVタイトル #{$id}",
            "thumbnail" => "https://placeholder.com{$category}-{$id}",
            "price" => rand(1000, 5000)
        ];
    }
}

// フロントエンドが受け取るJSONの構造
$response = [
    "page" => $page,
    "hasMore" => $hasMore,
    "games" => $games
];

echo json_encode($response);


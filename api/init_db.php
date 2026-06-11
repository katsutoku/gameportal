<?php
// header('Content-Type: application/html; charset=utf-8');
header('Content-Type: text/html; charset=utf-8');

$db_file = __DIR__ . '/users_db.json';

// すでにファイル（テーブル）がなければ作成
if (!file_exists($db_file)) {
    $initial_data = [
        [
            "id" => 1,
            "username" => "admin",
            "password_hash" => password_hash("admin123", PASSWORD_DEFAULT),
            "created_at" => date('Y-m-d H:i:s')
        ]
    ];
    // ファイルに書き込んで保存（これがテーブル作成と初期データ挿入にあたります）
    file_put_contents($db_file, json_encode($initial_data, JSON_PRETTY_PRINT));
    echo "【大成功】ファイルベースの模擬データベースを作成し、テストユーザー(admin)を登録しました！";
} else {
    echo "【確認】模擬データベースはすでに初期化されています。";
}

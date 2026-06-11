<?php
header('Content-Type: application/json; charset=utf-8');

// 本来は $_GET['id'] を受けてDB検索しますが、一先ず固定のダミーデータを返します
$response = [
    "title" => "レイヤード・ストーリー",
    "category" => "SFサスペンス",
    "release_date" => "2026年6月1日",
    "description" => "近未来の渋谷を舞台に、量子記憶デバイス『レイヤー』を巡る陰謀に巻き込まれた主人公たちの運命を描くビジュアルノベル。あなたの選択が、無数の世界線を紡ぎ出す。フルボイス＆マルチエンディング対応。",
    
    // カルーセル用の画像リスト
    "images" => [
        "https://placeholder.com",
        "https://placeholder.com",
        "https://placeholder.com",
        "https://placeholder.com"
    ],
    
    // アチーブメント
    "achievements" => [
        ["icon" => "👁️‍🗨️", "title" => "観測の始まり", "desc" => "プロローグをクリアした"],
        ["icon" => "🧩", "title" => "論理的思考", "desc" => "推理フェーズでノーミスクリア"],
        ["icon" => "⏳", "title" => "シュタインズの扉", "desc" => "すべてのエンディングを回収"]
    ],
    
    // DLC
    "dlcs" => [
        ["title" => "追加シナリオ：外伝『レイター・コード』", "price" => "¥1,200"],
        ["title" => "デジタルサウンドトラック＆アートブック", "price" => "¥800"]
    ],
    
    // お知らせ
    "news" => [
        "【6/11】アップデートVer.1.02を配信しました",
        "【6/05】公式ファンアートコンテスト開催決定！",
        "【6/01】本日配信開始！記念壁紙配布中"
    ]
];

echo json_encode($response);

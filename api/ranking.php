<?php
header('Content-Type: application/json; charset=utf-8');

$type = isset($_GET['type']) ? $_GET['type'] : 'A';

// タブ名マッピング（デバッグ表示用）
$typeName = ['A' => '人気', 'B' => '高評価', 'C' => '売上'];

$rankingData = [];

for ($rank = 1; $rank <= 5; $rank++) {
    // 前日比の変動をランダムに生成 (-3 ~ +3)
    $change = rand(-3, 3);
    
    $rankingData[] = [
        "rank" => $rank,
        "title" => "【{$typeName[$type]}】第{$rank}位のADVゲーム",
        "thumbnail" => "https://placeholder.com{$type}-Rank-{$rank}",
        "score" => number_format(5.0 - ($rank * 0.1) - (rand(0, 5) * 0.02), 1), // 4.9, 4.8...のような評価値
        "price" => $rank === 3 ? "無料" : "¥" . rand(1500, 6000),
        "change" => $change
    ];
}

echo json_encode($rankingData);


ディレクトリ構成（バニラ設計の王道）

├── public/                 # フロントエンド（ブラウザ公開領域）
│   ├── css/                # スタイルシート（共通・画面別）
│   ├── js/                 # JavaScript（無限スクロールやホバー処理）
│   │   ├── main.js         # 共通処理
│   │   ├── home.js         # ホーム画面用（無限スクロール等）
│   │   └── detail.js       # 詳細画面用
│   ├── images/             # サムネイル・アイコン等
│   ├── index.html          # ホーム画面
│   ├── login.html          # ログイン画面
│   ├── detail.html         # 詳細画面
│   ├── settings.html       # 設定画面
│   └── mygames.html        # マイゲーム画面
│
└── api/                    # バックエンド（PHP処理：JSONを返す）
    ├── config/             # DB接続設定
    ├── auth/               # ログイン・セッション管理（login.php等）
    ├── games/              # ゲームデータ取得（list.php, detail.php等）
    └── user/               # マイゲーム・設定更新

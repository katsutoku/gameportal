document.addEventListener('DOMContentLoaded', () => {

    // -----------------------------------------------------------------
    // common.jsに分離
    //
    // const userMenu = document.querySelector('.user-menu');

    // async function checkLoginStatus() {
    //     try {
    //         const response = await fetch('api/check_auth.php');
    //         const data = await response.json();

    //         if (data.isLoggedIn) {
    //             // ログイン中ならユーザー名とログアウトボタンを表示
    //             userMenu.innerHTML = `
    //             <span style="margin-right: 15px;">👤 ${data.username}</span>
    //             <button id="logoutBtn" style="background:none; border:1px solid #666; color:#aaa; padding:4px 8px; border-radius:4px; cursor:pointer;">ログアウト</button>
    //         `;

    //             // ログアウトボタンのイベント設定
    //             document.getElementById('logoutBtn').addEventListener('click', async () => {
    //                 const res = await fetch('api/logout.php');
    //                 const logoutData = await res.json();
    //                 if (logoutData.success) {
    //                     window.location.reload(); // 画面をリロードして未ログイン状態に戻す
    //                 }
    //             });
    //         } else {
    //             // 未ログインなら通常のログインボタンを表示
    //             userMenu.innerHTML = `<a href="login.html" class="btn-login" style="color: #fff; text-decoration: none;">ログイン</a>`;
    //         }
    //     } catch (error) {
    //         console.error('ログインステータスの確認に失敗しました:', error);
    //     }
    // }

    // // 実行
    // checkLoginStatus();

    // // 画面読み込み時にテーマをチェックして適用する
    // if (localStorage.getItem('portalTheme') === 'light') {
    //     document.body.classList.add('light-theme');
    // }
    // -----------------------------------------------------------------

    // 現在のページ数を管理するオブジェクト（リストごとに管理）
    const pageStatus = {
        'A': { currentPage: 1, isLoading: false, hasMore: true },
        'B': { currentPage: 1, isLoading: false, hasMore: true }
    };

    // ゲームカードを生成してDOMに追加する関数
    function appendGameCards(container, games) {
        const trigger = container.querySelector('.loading-trigger');

        games.forEach(game => {
            const card = document.createElement('div');
            card.className = 'game-card';

            // [home.js のカード生成箇所を以下のように修正]
            card.innerHTML = `
                <a href="detail.html?id=${game.id}" style="text-decoration: none; color: inherit;">
                    <img src="${game.thumbnail}" alt="${game.title}">
                    <div style="padding: 10px;">
                        <h4 style="margin:0; font-size:14px;">${game.title}</h4>
                        <p style="margin:5px 0 0; font-size:12px; color:#aaa;">¥${game.price}</p>
                    </div>
                </a>
            `;

            // 詳細画面へのリンクにする場合は、card全体をaタグにするかクリックイベントをつけてください
            container.insertBefore(card, trigger);
        });
    }

    // PHP APIからデータを取得する非同期関数
    async function loadNextPage(listId, targetType) {
        const status = pageStatus[targetType];

        // 読込中、またはこれ以上データがない場合はスキップ
        if (status.isLoading || !status.hasMore) return;

        status.isLoading = true;
        const container = document.getElementById(listId);

        try {
            // PHPのAPIを呼び出す（リストの種類とページ数をパラメータで渡す）
            const response = await fetch(`api/games.php?category=${targetType}&page=${status.currentPage}`);
            const data = await response.json();

            if (data.games && data.games.length > 0) {
                appendGameCards(container, data.games);
                status.currentPage++; // 次回用にページを進める
                status.hasMore = data.hasMore; // 次のページがあるか更新
            } else {
                status.hasMore = false;
            }
        } catch (error) {
            console.error('データ取得に失敗しました:', error);
        } finally {
            status.isLoading = false;
        }
    }

    // --- IntersectionObserver（無限スクロールの核心）の設定 ---
    const observerOptions = {
        root: null, // nullの場合はブラウザの画面全体を基準にする
        rootMargin: '0px 200px 0px 0px', // 右端に見える200px手前で先読みを開始する設定
        threshold: 0 // 1ピクセルでもトリガーが見えたら実行
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            // トリガー要素が画面（または指定した範囲）に入った場合
            if (entry.isIntersecting) {
                const trigger = entry.target;
                const targetType = trigger.getAttribute('data-target'); // 'A' または 'B'
                const listId = targetType === 'A' ? 'list-a' : 'list-b';

                // 次のページを読み込む
                loadNextPage(listId, targetType);
            }
        });
    }, observerOptions);

    // すべてのトリガー要素（目印）を監視対象に登録
    document.querySelectorAll('.loading-trigger').forEach(trigger => {
        observer.observe(trigger);
    });

    const featuredCard = document.getElementById('featuredGame');
    const video = featuredCard.querySelector('.featured-video');
    let hoverTimer = null;

    // マウスが乗ったときの処理
    featuredCard.addEventListener('mouseenter', () => {
        // 0.5秒（500ms）以上ホバーが続いたら動画を再生
        hoverTimer = setTimeout(() => {
            featuredCard.classList.add('is-playing');

            // 動画を最初から再生
            video.currentTime = 0;
            const playPromise = video.play();

            // ブラウザの自動再生ブロック対策のエラーハンドリング
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log("動画の自動再生がブロックされました:", error);
                });
            }
        }, 500); // 誤操作防止用のウェイト時間
    });

    // マウスが離れたときの処理
    featuredCard.addEventListener('mouseleave', () => {
        // タイマーが走っている途中ならキャンセル
        if (hoverTimer) {
            clearTimeout(hoverTimer);
        }

        // クラスを外して動画を停止
        featuredCard.classList.remove('is-playing');
        video.pause();
    });

    const rankingList = document.getElementById('rankingList');
    const tabButtons = document.querySelectorAll('.tab-btn');

    // ランキングデータをAPIから取得して描画する関数
    async function loadRanking(type) {
        rankingList.innerHTML = '<div style="padding:20px; color:#aaa;">読み込み中...</div>';

        try {
            const response = await fetch(`api/ranking.php?type=${type}`);
            const data = await response.json();

            rankingList.innerHTML = ''; // ローディング表示を消去

            data.forEach(item => {
                // 前日からの変動（change）に応じた矢印とクラスの判定
                let trendHTML = '';
                if (item.change > 0) {
                    trendHTML = `<span class="trend-up">▲ ${item.change}</span>`;
                } else if (item.change < 0) {
                    trendHTML = `<span class="trend-down">▼ ${Math.abs(item.change)}</span>`;
                } else {
                    trendHTML = `<span class="trend-stay">─</span>`;
                }

                // アイテムのHTML組み立て
                const row = document.createElement('div');
                row.className = 'ranking-item';
                row.innerHTML = `
                <div class="rank-num">${item.rank}</div>
                <div class="rank-trend">${trendHTML}</div>
                <img src="${item.thumbnail}" alt="" class="rank-thumb">
                <h3 class="rank-title">${item.title}</h3>
                <div class="rank-score">⭐ ${item.score}</div>
                <div class="rank-price">${item.price}</div>
            `;
                rankingList.appendChild(row);
            });
        } catch (error) {
            rankingList.innerHTML = '<div style="padding:20px; color:#ff3838;">データの取得に失敗しました。</div>';
            console.error(error);
        }
    }

    // タブボタンのクリックイベント設定
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // アクティブなボタンのクラスを切り替え
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // 属性値（A/B/C）を取得してランキングを再読込
            const type = button.getAttribute('data-rank');
            loadRanking(type);
        });
    });

    // 初回読み込み時は「A（人気急上昇）」を表示
    loadRanking('A');

    // --- [home.js の末尾（DOMContentLoaded内）に追記] ---

    function enableMouseSwipe() {
        const sliders = document.querySelectorAll('.scroll-row');

        sliders.forEach(slider => {
            let isDown = false;
            let startX;
            let scrollLeft;

            // 1. マウスボタンが押された瞬間
            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                slider.style.cursor = 'grabbing'; // 掴んでいる手のアイコンに変える
                slider.style.scrollBehavior = 'auto'; // ドラッグ中はスムーズスクロールを無効にして追従性を上げる
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });

            // 2. マウスが要素から外れた、またはボタンが離された瞬間
            const stopDragging = () => {
                if (!isDown) return;
                isDown = false;
                slider.style.cursor = 'pointer';
                slider.style.scrollBehavior = 'smooth'; // ドラッグが終わったらスムーズスクロールに戻す
            };
            slider.addEventListener('mouseleave', stopDragging);
            slider.addEventListener('mouseup', stopDragging);

            // 3. マウスが押された状態で動いている最中
            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return; // 押されていなければ何もしない
                e.preventDefault(); // テキスト選択や画像のドラッグを防ぐ

                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 2; // 「* 2」でスクロールの感度（スピード）を調整
                slider.scrollLeft = scrollLeft - walk;
            });

            // 【UX対策】ドラッグ中にカード内のリンク（aタグ）をクリックしてしまう誤作動を防ぐ
            slider.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', (e) => {
                    // 大きくドラッグした（walkの絶対値が大きい）場合はクリックをキャンセルする
                    if (slider.style.cursor === 'grabbing') {
                        // 少しでも動いていたら、1ページ目への遷移を一時的にブロック可能ですが、
                        // 今回はバニラJSの標準仕様に沿って、ドラッグ直後のクリック誤判定をブラウザのデフォルトに任せます
                    }
                });
            });
        });
    }

    // 実行する
    enableMouseSwipe();

});

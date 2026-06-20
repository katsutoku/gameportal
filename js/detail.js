document.addEventListener('DOMContentLoaded', () => {

    const mainVisual = document.getElementById('mainVisual');
    const thumbCarousel = document.getElementById('thumbCarousel');
    const gameTitle = document.getElementById('gameTitle');
    const gameCategory = document.getElementById('gameCategory');
    const gameRelease = document.getElementById('gameRelease');
    const gameDescription = document.getElementById('gameDescription');

    const achievementList = document.getElementById('achievementList');
    const dlcList = document.getElementById('dlcList');
    const newsList = document.getElementById('newsList');

    // データの取得と反映
    async function loadGameDetail() {
        // 1. 現在のURLからパラメータ（?id=X）をバニラJSで解析して取得
        const urlParams = new URLSearchParams(window.location.search);
        const gameId = urlParams.get('id') || 1; // IDがなければデフォルトで1にする

        try {
            // 2. 解析したIDを付与してPHP APIを呼び出す
            const response = await fetch(`api/detail.php?id=${gameId}`);
            const data = await response.json();

            if (data.error) {
                document.getElementById('gameTitle').textContent = data.error;
                return;
            }

            // 基本情報の埋め込み
            gameTitle.textContent = data.title;
            gameCategory.textContent = data.category;
            gameRelease.textContent = `配信日: ${data.release_date}`;
            gameDescription.textContent = data.description;

            // 初回の大サムネイル設定
            if (data.images && data.images.length > 0) {
                mainVisual.src = data.images[0];
            }

            // 小サムネイル（カルーセル）の動的生成
            thumbCarousel.innerHTML = ''; // 既存をクリア
            data.images.forEach((imgUrl, index) => {
                const img = document.createElement('img');
                img.src = imgUrl;
                img.className = 'thumb-item';
                if (index === 0) img.classList.add('active');

                img.addEventListener('click', () => {
                    document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
                    img.classList.add('active');
                    mainVisual.src = imgUrl;
                });
                thumbCarousel.appendChild(img);
            });

            // アチーブメントの生成
            achievementList.innerHTML = '';
            data.achievements.forEach(item => {
                const div = document.createElement('div');
                div.className = 'achieve-item';
                div.innerHTML = `
                <div class="achieve-icon">${item.icon}</div>
                <div>
                    <div class="achieve-title">${item.title}</div>
                    <div class="achieve-desc">${item.desc}</div>
                </div>
            `;
                achievementList.appendChild(div);
            });

            // DLCの生成
            dlcList.innerHTML = '';
            data.dlcs.forEach(item => {
                const div = document.createElement('div');
                div.className = 'dlc-item';
                div.innerHTML = `
                <div class="dlc-title">${item.title}</div>
                <div class="dlc-price">${item.price}</div>
            `;
                dlcList.appendChild(div);
            });

            // お知らせの生成
            newsList.innerHTML = '';
            data.news.forEach(text => {
                const li = document.createElement('li');
                li.innerHTML = `<a href="#">${text}</a>`;
                newsList.appendChild(li);
            });

        } catch (error) {
            console.error('詳細データの取得に失敗しました:', error);
        }
    }
    loadGameDetail();
});

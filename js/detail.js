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
        try {
            const response = await fetch('api/detail.php');
            const data = await response.json();

            // 基本情報の埋め込み
            gameTitle.textContent = data.title;
            gameCategory.textContent = data.category;
            gameRelease.textContent = `配信日: ${data.release_date}`;
            gameDescription.textContent = data.description;
            
            // 初回の大サムネイル設定
            if(data.images.length > 0) {
                mainVisual.src = data.images[0];
            }

            // 小サムネイル（カルーセル）の動的生成
            data.images.forEach((imgUrl, index) => {
                const img = document.createElement('img');
                img.src = imgUrl;
                img.className = 'thumb-item';
                if(index === 0) img.classList.add('active'); // 1枚目をアクティブに

                // 【ギミック】クリック時に大画像を切り替えるイベント
                img.addEventListener('click', () => {
                    // 全体のactiveを解除して、クリックされた要素に付与
                    document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
                    img.classList.add('active');
                    // 大画像のsrcを書き換え
                    mainVisual.src = imgUrl;
                });

                thumbCarousel.appendChild(img);
            });

            // アチーブメントの生成
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

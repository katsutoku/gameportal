// 全画面共通の初期化・ユーティリティ
document.addEventListener('DOMContentLoaded', () => {
    // 1. カラーテーマの自動適用（設定画面での変更を全画面に引き継ぐ）
    if (localStorage.getItem('portalTheme') === 'light') {
        document.body.classList.add('light-theme');
    }

    // 2. ヘッダーのログイン状態を動的更新
    const userMenu = document.querySelector('.user-menu') || document.getElementById('userMenu');
    if (userMenu) {
        checkGlobalLoginStatus(userMenu);
    }
});

// ログイン状態を確認してヘッダーを書き換える関数
async function checkGlobalLoginStatus(container) {
    try {
        const response = await fetch('api/check_auth.php');
        const data = await response.json();

        if (data.isLoggedIn) {
            container.innerHTML = `
                <span style="margin-right: 15px;">👤 ${data.username}</span>
                <button id="globalLogoutBtn" style="background:none; border:1px solid #666; color:#aaa; padding:4px 8px; border-radius:4px; cursor:pointer;">ログアウト</button>
            `;

            document.getElementById('globalLogoutBtn').addEventListener('click', async () => {
                const res = await fetch('api/logout.php');
                const logoutData = await res.json();
                if (logoutData.success) {
                    window.location.href = 'index.html'; // ログアウト後はホームへ
                }
            });
        } else {
            container.innerHTML = `<a href="login.html" class="btn-login" style="color: #fff; text-decoration: none;">ログイン</a>`;
        }
    } catch (error) {
        console.error('認証確認エラー:', error);
    }
}

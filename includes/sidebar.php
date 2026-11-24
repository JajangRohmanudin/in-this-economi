<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<div id="sidebarBackdrop" class="sidebar-backdrop" aria-hidden="true"></div>

<aside id="appSidebar" class="sidebar" aria-label="Navigasi Utama">
    <div class="sidebar-top">
        <h2>In This Economi</h2>
        <button type="button" id="closeSidebarBtn" class="sidebar-close" aria-label="Tutup Menu">&times;</button>
    </div>

    <a href="/in-this-economi/dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">📊 Dashboard Utama</a>
    <a href="/in-this-economi/daily/index.php" class="<?= ($current_dir == 'daily') ? 'active' : ''; ?>">💸 Keuangan Sehari-hari</a>
    <a href="/in-this-economi/stocks/index.php" class="<?= ($current_dir == 'stocks') ? 'active' : ''; ?>">📈 Portofolio Saham</a>
    <a href="/in-this-economi/owner/index.php" class="<?= ($current_dir == 'owner') ? 'active' : ''; ?>">👑 Owner Hub (Usaha)</a>

    <div style="margin-top: 36px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 16px;">
        <a href="/in-this-economi/auth/logout.php"
           class="js-confirm-trigger"
           data-mode="delete"
           data-title="Keluar Aplikasi"
           data-message="Yakin ingin keluar dari akunmu sekarang?"
           data-label="Ya, Keluar"
           style="color: #f87171; font-weight: 600;">🚪 Keluar (Logout)</a>
    </div>
</aside>

<script>
(function () {
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const closeBtn = document.getElementById('closeSidebarBtn');
    if (!sidebar || !backdrop) return;

    function openSidebar() {
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
    }

    window.toggleAppSidebar = function () {
        if (sidebar.classList.contains('is-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    };

    document.addEventListener('click', function (e) {
        const opener = e.target.closest('.js-open-sidebar');
        if (opener) {
            e.preventDefault();
            openSidebar();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    backdrop.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
            closeSidebar();
        }
    });
})();
</script>
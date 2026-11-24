<?php
require_once 'config/database.php';
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In This Economi — Catatan Finansial & Usaha</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="landing-page">

    <div class="landing-wrap">
        <header class="landing-nav">
            <a href="index.php" class="brand-mark">
                <span class="brand-dot"></span>
                <span>In This Economi</span>
            </a>
            <div class="page-actions">
                <a href="auth/login.php" class="btn btn-secondary">Masuk</a>
                <a href="auth/register.php" class="btn">Buat Akun</a>
            </div>
        </header>

        <section class="hero-editorial">
            <div>
                <span class="eyebrow-pill">RUANG KENDALI KAS &amp; USAHA</span>
                <h1>Catat arus kas harian, portofolio, dan <span>laba usaha</span> dalam satu meja.</h1>
                <p>Dirancang untuk pencatatan yang tenang, jelas, dan terpisah per akun—mulai dari pengeluaran harian, kalkulasi unit aset, hingga rekap modal dan penjualan.</p>
                <div class="page-actions">
                    <a href="auth/register.php" class="btn">Mulai Catat Sekarang</a>
                    <a href="auth/login.php" class="btn btn-secondary">Sudah Punya Akun</a>
                </div>
            </div>

            <div class="preview-stack">
                <div class="preview-row">
                    <div>
                        <strong>Arus Kas Harian</strong>
                        <small>Pemasukan &amp; pengeluaran terpisah rapi</small>
                    </div>
                    <span class="preview-tag">Harian</span>
                </div>
                <div class="preview-row">
                    <div>
                        <strong>Portofolio Saham &amp; Crypto</strong>
                        <small>Hitung unit otomatis dari total modal</small>
                    </div>
                    <span class="preview-tag">Investasi</span>
                </div>
                <div class="preview-row">
                    <div>
                        <strong>Owner Hub Usaha</strong>
                        <small>Omzet, bahan baku, laba kotor &amp; bersih</small>
                    </div>
                    <span class="preview-tag">Bisnis</span>
                </div>
            </div>
        </section>

        <div class="section-heading">
            <h2>Tiga ruang kerja, satu alur pencatatan</h2>
            <p>Setiap modul menyimpan data sesuai akun yang sedang aktif tanpa tercampur.</p>
        </div>

        <div class="card-grid">
            <article class="card">
                <h3>01 · Harian</h3>
                <div class="value" style="font-size: 1.15rem; margin-bottom: 8px;">Keuangan Sehari-hari</div>
                <p style="margin: 0; color: var(--ink-muted); font-size: 0.9rem; line-height: 1.55;">Catat pemasukan dan pengeluaran rutin dengan kategori, catatan, serta pengeditan yang mudah.</p>
            </article>

            <article class="card">
                <h3>02 · Investasi</h3>
                <div class="value" style="font-size: 1.15rem; margin-bottom: 8px;">Portofolio Aset</div>
                <p style="margin: 0; color: var(--ink-muted); font-size: 0.9rem; line-height: 1.55;">Input total dana yang dikeluarkan dan harga per unit untuk menghitung porsi unit secara otomatis.</p>
            </article>

            <article class="card">
                <h3>03 · Operasional</h3>
                <div class="value" style="font-size: 1.15rem; margin-bottom: 8px;">Owner Hub</div>
                <p style="margin: 0; color: var(--ink-muted); font-size: 0.9rem; line-height: 1.55;">Pantau penjualan produk, biaya bahan baku, dan pengeluaran operasional untuk melihat laba bersih.</p>
            </article>
        </div>
    </div>

</body>
</html>
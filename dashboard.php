<?php
require_once 'config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$total_masuk = 0;
$total_keluar = 0;
$total_modal_saham = 0;
$omzet_usaha = 0;

try {
    $daily_summary = $pdo->prepare("SELECT 
        SUM(CASE WHEN type = 'in' THEN amount ELSE 0 END) as total_in,
        SUM(CASE WHEN type = 'out' THEN amount ELSE 0 END) as total_out
        FROM daily_finance WHERE user_id = ?");
    $daily_summary->execute([$user_id]);
    $daily = $daily_summary->fetch(PDO::FETCH_ASSOC);

    $total_masuk = (float) ($daily['total_in'] ?? 0);
    $total_keluar = (float) ($daily['total_out'] ?? 0);

    $stock_summary = $pdo->prepare("SELECT SUM(buy_price * shares_qty) as total_stock FROM stock_portfolio WHERE user_id = ?");
    $stock_summary->execute([$user_id]);
    $total_modal_saham = (float) ($stock_summary->fetch(PDO::FETCH_ASSOC)['total_stock'] ?? 0);

    $total_revenue = $pdo->prepare("SELECT SUM(selling_price * qty) as total FROM business_sales WHERE user_id = ?");
    $total_revenue->execute([$user_id]);
    $omzet_usaha = (float) ($total_revenue->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
} catch (PDOException $e) {
    // Abaikan jika tabel kosong
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - In This Economi</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-page">

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Halo, <?= htmlspecialchars($username); ?>!</h1>
                </div>
                <p>Selamat datang kembali di pusat kendali finansial dan usahamu.</p>
            </div>
            <div class="page-actions">
                <a href="daily/index.php" class="btn" style="background-color: #0284c7;">Keuangan Harian</a>
                <a href="stocks/index.php" class="btn" style="background-color: #2563eb;">Portofolio Saham</a>
                <a href="owner/index.php" class="btn">Owner Hub</a>
            </div>
        </div>

        <div class="card-grid">
            <div class="card">
                <h3>Total Pemasukan Harian</h3>
                <div class="value" style="color: var(--success);">Rp <?= number_format($total_masuk, 0, ',', '.'); ?></div>
            </div>

            <div class="card">
                <h3>Total Pengeluaran Harian</h3>
                <div class="value" style="color: var(--danger);">Rp <?= number_format($total_keluar, 0, ',', '.'); ?></div>
            </div>

            <div class="card">
                <h3>Total Modal Portofolio Saham</h3>
                <div class="value" style="color: #2563eb;">Rp <?= number_format($total_modal_saham, 0, ',', '.'); ?></div>
            </div>

            <div class="card">
                <h3>Total Omzet Usaha</h3>
                <div class="value" style="color: var(--pink-hover);">Rp <?= number_format($omzet_usaha, 0, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <?php include 'includes/confirm_modal.php'; ?>

</body>
</html>
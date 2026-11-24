<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];

$rev_stmt = $pdo->prepare("SELECT SUM(selling_price * qty) AS total FROM business_sales WHERE user_id = ?");
$rev_stmt->execute([$user_id]);
$total_revenue = (float) ($rev_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

$mat_stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM business_expenses WHERE user_id = ? AND expense_type = 'Bahan Baku'");
$mat_stmt->execute([$user_id]);
$total_material = (float) ($mat_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

$oth_stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM business_expenses WHERE user_id = ? AND expense_type != 'Bahan Baku'");
$oth_stmt->execute([$user_id]);
$total_other_expenses = (float) ($oth_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

$gross_profit = $total_revenue - $total_material;
$net_profit = $gross_profit - $total_other_expenses;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Hub - In This Economi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Owner Hub (Manajemen Usaha)</h1>
                </div>
                <p>Pusat kendali operasional, penjualan, dan laporan laba-rugi bisnis.</p>
            </div>
            <div class="page-actions">
                <a href="sales.php" class="btn">Kelola Penjualan</a>
                <a href="expenses.php" class="btn btn-secondary">Kelola Pengeluaran</a>
            </div>
        </div>

        <div class="card-grid" style="margin-bottom: 24px;">
            <div class="card">
                <h3>Total Omzet (Pendapatan)</h3>
                <div class="value" style="color: var(--success);">Rp <?= number_format($total_revenue, 0, ',', '.'); ?></div>
            </div>

            <div class="card">
                <h3>Modal Bahan Baku</h3>
                <div class="value" style="color: var(--danger);">Rp <?= number_format($total_material, 0, ',', '.'); ?></div>
            </div>

            <div class="card">
                <h3>Laba Kotor (Gross Profit)</h3>
                <div class="value" style="color: var(--pink-hover);">Rp <?= number_format($gross_profit, 0, ',', '.'); ?></div>
            </div>

            <div class="card">
                <h3>Laba Bersih (Net Profit)</h3>
                <div class="value" style="color: <?= ($net_profit >= 0) ? 'var(--success)' : 'var(--danger)'; ?>;">
                    Rp <?= number_format($net_profit, 0, ',', '.'); ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 10px; color: var(--ink);">Informasi Perhitungan Bisnis</h3>
            <ul style="padding-left: 20px; color: var(--ink-muted); line-height: 1.6; margin: 0;">
                <li><strong>Laba Kotor:</strong> Total Omzet Penjualan dikurangi Total Modal Bahan Baku.</li>
                <li><strong>Laba Bersih:</strong> Laba Kotor dikurangi Biaya Gaji/Borongan dan Operasional Lainnya.</li>
            </ul>
        </div>
    </div>

    <?php include '../includes/confirm_modal.php'; ?>

</body>
</html>
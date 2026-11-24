<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM business_sales WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: sales.php");
    exit;
}

$stmt = $pdo->prepare("SELECT *, (selling_price * qty) AS total_sale FROM business_sales WHERE user_id = ? ORDER BY sale_date DESC, id DESC");
$stmt->execute([$user_id]);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penjualan - Owner Hub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Riwayat Penjualan Produk</h1>
                </div>
                <p>Daftar seluruh produk usaha milikmu yang berhasil terjual.</p>
            </div>
            <div class="page-actions">
                <a href="sales_add.php" class="btn">+ Tambah Penjualan</a>
                <a href="index.php" class="btn btn-secondary">Kembali ke Hub</a>
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Produk</th>
                            <th>Harga Satuan</th>
                            <th>Qty</th>
                            <th>Total Pendapatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--ink-muted); padding: 30px;">Belum ada data penjualan tercatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['sale_date']); ?></td>
                                    <td><strong><?= htmlspecialchars($s['product_name']); ?></strong></td>
                                    <td>Rp <?= number_format((float) $s['selling_price'], 0, ',', '.'); ?></td>
                                    <td><?= (int) $s['qty']; ?> pcs</td>
                                    <td style="color: var(--success); font-weight: 600;">Rp <?= number_format((float) $s['total_sale'], 0, ',', '.'); ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="sales_edit.php?id=<?= (int) $s['id']; ?>"
                                               class="action-pill action-pill-edit js-confirm-trigger"
                                               data-mode="edit"
                                               data-title="Konfirmasi Edit"
                                               data-message="Yakin ingin mengedit data penjualan '<?= htmlspecialchars($s['product_name'], ENT_QUOTES); ?>'?"
                                               data-label="Ya, Edit">Edit</a>
                                            <a href="sales.php?delete=<?= (int) $s['id']; ?>"
                                               class="action-pill action-pill-delete js-confirm-trigger"
                                               data-mode="delete"
                                               data-title="Konfirmasi Hapus"
                                               data-message="Yakin ingin menghapus data penjualan '<?= htmlspecialchars($s['product_name'], ENT_QUOTES); ?>'?"
                                               data-label="Ya, Hapus">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include '../includes/confirm_modal.php'; ?>

</body>
</html>
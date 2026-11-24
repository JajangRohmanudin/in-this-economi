<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM daily_finance WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM daily_finance WHERE user_id = ? ORDER BY date DESC, id DESC");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Keuangan - In This Economi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Riwayat Keuangan Sehari-hari</h1>
                </div>
                <p>Daftar seluruh arus kas masuk dan keluar harian milikmu.</p>
            </div>
            <div class="page-actions">
                <a href="add.php" class="btn">+ Tambah Transaksi</a>
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--ink-muted); padding: 30px;">Belum ada data transaksi harian.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t['date']); ?></td>
                                    <td>
                                        <?php if ($t['type'] == 'in'): ?>
                                            <span style="color: var(--success); font-weight: 600;">Pemasukan</span>
                                        <?php else: ?>
                                            <span style="color: var(--danger); font-weight: 600;">Pengeluaran</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($t['category']); ?></td>
                                    <td>Rp <?= number_format((float) $t['amount'], 0, ',', '.'); ?></td>
                                    <td><?= htmlspecialchars($t['note']); ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="edit.php?id=<?= (int) $t['id']; ?>"
                                               class="action-pill action-pill-edit js-confirm-trigger"
                                               data-mode="edit"
                                               data-title="Konfirmasi Edit"
                                               data-message="Yakin ingin mengedit transaksi '<?= htmlspecialchars($t['category'], ENT_QUOTES); ?>'?"
                                               data-label="Ya, Edit">Edit</a>
                                            <a href="index.php?delete=<?= (int) $t['id']; ?>"
                                               class="action-pill action-pill-delete js-confirm-trigger"
                                               data-mode="delete"
                                               data-title="Konfirmasi Hapus"
                                               data-message="Yakin ingin menghapus transaksi '<?= htmlspecialchars($t['category'], ENT_QUOTES); ?>'?"
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
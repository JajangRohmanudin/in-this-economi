<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM business_expenses WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: expenses.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM business_expenses WHERE user_id = ? ORDER BY expense_date DESC, id DESC");
$stmt->execute([$user_id]);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengeluaran - Owner Hub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Riwayat Pengeluaran Bisnis</h1>
                </div>
                <p>Daftar modal bahan baku, gaji, dan operasional usaha.</p>
            </div>
            <div class="page-actions">
                <a href="expenses_add.php" class="btn">+ Tambah Pengeluaran</a>
                <a href="index.php" class="btn btn-secondary">Kembali ke Hub</a>
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--ink-muted); padding: 30px;">Belum ada data pengeluaran usaha.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $exp): ?>
                                <tr>
                                    <td><?= htmlspecialchars($exp['expense_date']); ?></td>
                                    <td><strong style="color: var(--pink-hover);"><?= htmlspecialchars($exp['expense_type']); ?></strong></td>
                                    <td><?= htmlspecialchars($exp['description']); ?></td>
                                    <td style="color: var(--danger); font-weight: 600;">Rp <?= number_format((float) $exp['amount'], 0, ',', '.'); ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="expenses_edit.php?id=<?= (int) $exp['id']; ?>"
                                               class="action-pill action-pill-edit js-confirm-trigger"
                                               data-mode="edit"
                                               data-title="Konfirmasi Edit"
                                               data-message="Yakin ingin mengedit pengeluaran '<?= htmlspecialchars($exp['description'], ENT_QUOTES); ?>'?"
                                               data-label="Ya, Edit">Edit</a>
                                            <a href="expenses.php?delete=<?= (int) $exp['id']; ?>"
                                               class="action-pill action-pill-delete js-confirm-trigger"
                                               data-mode="delete"
                                               data-title="Konfirmasi Hapus"
                                               data-message="Yakin ingin menghapus pengeluaran '<?= htmlspecialchars($exp['description'], ENT_QUOTES); ?>'?"
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
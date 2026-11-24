<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$id = (int) ($_GET['id'] ?? 0);
$error = '';

$stmt = $pdo->prepare("SELECT * FROM daily_finance WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$trx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trx) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'out';
    $category = trim($_POST['category'] ?? '');
    $amount = (float) ($_POST['amount'] ?? 0);
    $note = trim($_POST['note'] ?? '');
    $date = $_POST['date'] ?? date('Y-m-d');

    if (!in_array($type, ['in', 'out'], true) || $category === '' || $amount <= 0) {
        $error = 'Tipe, kategori, dan nominal wajib diisi lebih dari 0.';
    } else {
        $upd = $pdo->prepare("UPDATE daily_finance SET type = ?, category = ?, amount = ?, note = ?, date = ? WHERE id = ? AND user_id = ?");
        $upd->execute([$type, $category, $amount, $note, $date, $id, $user_id]);
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - In This Economi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Edit Transaksi Harian</h1>
                </div>
                <p>Perbarui catatan transaksi harian (ketik angka polos tanpa titik).</p>
            </div>
        </div>

        <div class="card" style="max-width: 640px;">
            <form method="POST">
                <label for="type">Tipe Transaksi:</label>
                <select id="type" name="type">
                    <option value="out" <?= ($trx['type'] === 'out') ? 'selected' : ''; ?>>Pengeluaran</option>
                    <option value="in" <?= ($trx['type'] === 'in') ? 'selected' : ''; ?>>Pemasukan</option>
                </select>

                <label for="category">Kategori:</label>
                <input type="text" id="category" name="category" value="<?= htmlspecialchars($trx['category']); ?>" required>

                <label for="amount">Nominal (Rp):</label>
                <input type="number" id="amount" name="amount" step="any" min="0" value="<?= htmlspecialchars((string) $trx['amount']); ?>" required>

                <label for="note">Catatan:</label>
                <textarea id="note" name="note"><?= htmlspecialchars($trx['note'] ?? ''); ?></textarea>

                <label for="date">Tanggal:</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($trx['date']); ?>" required>

                <div class="page-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/confirm_modal.php'; ?>
    <?php if ($error !== ''): ?>
    <script>
        window.showAppPopup({
            type: 'error',
            title: 'Perubahan Gagal',
            message: <?= json_encode($error); ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>
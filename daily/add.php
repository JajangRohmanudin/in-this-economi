<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? 'out';
    $category = trim($_POST['category'] ?? '');
    $amount = (float) ($_POST['amount'] ?? 0);
    $note = trim($_POST['note'] ?? '');
    $date = $_POST['date'] ?? date('Y-m-d');

    if (!in_array($type, ['in', 'out'], true) || $category === '' || $amount <= 0) {
        $error = 'Tipe, kategori, dan nominal wajib diisi lebih dari 0.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO daily_finance (user_id, type, category, amount, note, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $type, $category, $amount, $note, $date]);
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
    <title>Tambah Transaksi - In This Economi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Tambah Transaksi Harian</h1>
                </div>
                <p>Catat pemasukan atau pengeluaran harian (ketik angka polos tanpa titik).</p>
            </div>
        </div>

        <div class="card" style="max-width: 640px;">
            <form method="POST">
                <label for="type">Tipe Transaksi:</label>
                <select id="type" name="type">
                    <option value="out">Pengeluaran</option>
                    <option value="in">Pemasukan</option>
                </select>

                <label for="category">Kategori:</label>
                <input type="text" id="category" name="category" placeholder="Contoh: Makan, Transport, Gaji" required>

                <label for="amount">Nominal (Rp):</label>
                <input type="number" id="amount" name="amount" step="any" min="0" placeholder="Contoh: 50000" required>

                <label for="note">Catatan:</label>
                <textarea id="note" name="note" placeholder="Catatan tambahan (opsional)"></textarea>

                <label for="date">Tanggal:</label>
                <input type="date" id="date" name="date" value="<?= date('Y-m-d'); ?>" required>

                <div class="page-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn">Simpan Transaksi</button>
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
            title: 'Input Belum Lengkap',
            message: <?= json_encode($error); ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>
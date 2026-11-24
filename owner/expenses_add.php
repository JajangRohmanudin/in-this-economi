<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_type = trim($_POST['expense_type'] ?? 'Bahan Baku');
    $description = trim($_POST['description'] ?? '');
    $amount = (float) ($_POST['amount'] ?? 0);
    $expense_date = $_POST['expense_date'] ?? date('Y-m-d');

    if ($description === '' || $amount <= 0) {
        $error = 'Keterangan dan nominal pengeluaran wajib diisi lebih dari 0.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO business_expenses (user_id, expense_type, description, amount, expense_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $expense_type, $description, $amount, $expense_date]);
        header("Location: expenses.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengeluaran - Owner Hub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Tambah Pengeluaran Usaha</h1>
                </div>
                <p>Catat modal bahan baku, produksi, atau operasional (ketik angka polos tanpa titik).</p>
            </div>
        </div>

        <div class="card" style="max-width: 640px;">
            <form method="POST">
                <label for="expense_type">Kategori Pengeluaran:</label>
                <select id="expense_type" name="expense_type">
                    <option value="Bahan Baku">Bahan Baku</option>
                    <option value="Gaji / Borongan">Gaji / Borongan</option>
                    <option value="Operasional">Operasional</option>
                    <option value="Pemasaran">Pemasaran</option>
                </select>

                <label for="description">Keterangan:</label>
                <input type="text" id="description" name="description" placeholder="Contoh: Kain heavy cotton & chenille patch" required>

                <label for="amount">Nominal Pengeluaran (Rp):</label>
                <input type="number" id="amount" name="amount" step="any" min="0" placeholder="Contoh: 5000000" required>

                <label for="expense_date">Tanggal Pengeluaran:</label>
                <input type="date" id="expense_date" name="expense_date" value="<?= date('Y-m-d'); ?>" required>

                <div class="page-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn">Simpan Pengeluaran</button>
                    <a href="expenses.php" class="btn btn-secondary">Batal</a>
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
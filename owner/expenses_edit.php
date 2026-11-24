<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$id = (int) ($_GET['id'] ?? 0);
$error = '';

$stmt = $pdo->prepare("SELECT * FROM business_expenses WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$exp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exp) {
    header("Location: expenses.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_type = trim($_POST['expense_type'] ?? 'Bahan Baku');
    $description = trim($_POST['description'] ?? '');
    $amount = (float) ($_POST['amount'] ?? 0);
    $expense_date = $_POST['expense_date'] ?? date('Y-m-d');

    if ($description === '' || $amount <= 0) {
        $error = 'Keterangan dan nominal pengeluaran wajib diisi lebih dari 0.';
    } else {
        $upd = $pdo->prepare("UPDATE business_expenses SET expense_type = ?, description = ?, amount = ?, expense_date = ? WHERE id = ? AND user_id = ?");
        $upd->execute([$expense_type, $description, $amount, $expense_date, $id, $user_id]);
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
    <title>Edit Pengeluaran - Owner Hub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Edit Pengeluaran Usaha</h1>
                </div>
                <p>Perbarui data pengeluaran usaha (ketik angka polos tanpa titik).</p>
            </div>
        </div>

        <div class="card" style="max-width: 640px;">
            <form method="POST">
                <label for="expense_type">Kategori Pengeluaran:</label>
                <select id="expense_type" name="expense_type">
                    <?php foreach (['Bahan Baku', 'Gaji / Borongan', 'Operasional', 'Pemasaran'] as $opt): ?>
                        <option value="<?= htmlspecialchars($opt); ?>" <?= ($exp['expense_type'] === $opt) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($opt); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="description">Keterangan:</label>
                <input type="text" id="description" name="description" value="<?= htmlspecialchars($exp['description']); ?>" required>

                <label for="amount">Nominal Pengeluaran (Rp):</label>
                <input type="number" id="amount" name="amount" step="any" min="0" value="<?= htmlspecialchars((string) $exp['amount']); ?>" required>

                <label for="expense_date">Tanggal Pengeluaran:</label>
                <input type="date" id="expense_date" name="expense_date" value="<?= htmlspecialchars($exp['expense_date']); ?>" required>

                <div class="page-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn">Simpan Perubahan</button>
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
            title: 'Perubahan Gagal',
            message: <?= json_encode($error); ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>
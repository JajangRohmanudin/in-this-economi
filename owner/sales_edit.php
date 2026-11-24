<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$id = (int) ($_GET['id'] ?? 0);
$error = '';

$stmt = $pdo->prepare("SELECT * FROM business_sales WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    header("Location: sales.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $selling_price = (float) ($_POST['selling_price'] ?? 0);
    $qty = (int) ($_POST['qty'] ?? 0);
    $sale_date = $_POST['sale_date'] ?? date('Y-m-d');

    if ($product_name === '' || $selling_price <= 0 || $qty <= 0) {
        $error = 'Nama produk, harga jual satuan, dan jumlah terjual wajib diisi.';
    } else {
        $upd = $pdo->prepare("UPDATE business_sales SET product_name = ?, selling_price = ?, qty = ?, sale_date = ? WHERE id = ? AND user_id = ?");
        $upd->execute([$product_name, $selling_price, $qty, $sale_date, $id, $user_id]);
        header("Location: sales.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Penjualan - Owner Hub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Edit Penjualan Produk</h1>
                </div>
                <p>Perbarui data transaksi penjualan usaha (ketik angka polos tanpa titik).</p>
            </div>
        </div>

        <div class="card" style="max-width: 640px;">
            <form method="POST">
                <label for="product_name">Nama Produk:</label>
                <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($sale['product_name']); ?>" required>

                <label for="selling_price">Harga Jual Satuan (Rp):</label>
                <input type="number" id="selling_price" name="selling_price" step="any" min="0" value="<?= htmlspecialchars((string) $sale['selling_price']); ?>" required>

                <label for="qty">Jumlah Terjual:</label>
                <input type="number" id="qty" name="qty" step="1" min="1" value="<?= (int) $sale['qty']; ?>" required>

                <label for="sale_date">Tanggal Penjualan:</label>
                <input type="date" id="sale_date" name="sale_date" value="<?= htmlspecialchars($sale['sale_date']); ?>" required>

                <div class="page-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn">Simpan Perubahan</button>
                    <a href="sales.php" class="btn btn-secondary">Batal</a>
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
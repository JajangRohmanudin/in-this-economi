<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $selling_price = (float) ($_POST['selling_price'] ?? 0);
    $qty = (int) ($_POST['qty'] ?? 0);
    $sale_date = $_POST['sale_date'] ?? date('Y-m-d');

    if ($product_name === '' || $selling_price <= 0 || $qty <= 0) {
        $error = 'Nama produk, harga jual satuan, dan jumlah terjual wajib diisi.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO business_sales (user_id, product_name, selling_price, qty, sale_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $product_name, $selling_price, $qty, $sale_date]);
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
    <title>Tambah Penjualan - Owner Hub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Tambah Penjualan Baru</h1>
                </div>
                <p>Catat transaksi penjualan produk usaha (ketik angka polos tanpa titik).</p>
            </div>
        </div>

        <div class="card" style="max-width: 640px;">
            <form method="POST">
                <label for="product_name">Nama Produk:</label>
                <input type="text" id="product_name" name="product_name" placeholder="Contoh: Chivalcry Hoodie" required>

                <label for="selling_price">Harga Jual Satuan (Rp):</label>
                <input type="number" id="selling_price" name="selling_price" step="any" min="0" placeholder="Contoh: 170000" required>

                <label for="qty">Jumlah Terjual:</label>
                <input type="number" id="qty" name="qty" step="1" min="1" placeholder="Contoh: 150" required>

                <label for="sale_date">Tanggal Penjualan:</label>
                <input type="date" id="sale_date" name="sale_date" value="<?= date('Y-m-d'); ?>" required>

                <div class="page-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn">Simpan Penjualan</button>
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
            title: 'Input Belum Lengkap',
            message: <?= json_encode($error); ?>
        });
    </script>
    <?php endif; ?>

</body>
</html>
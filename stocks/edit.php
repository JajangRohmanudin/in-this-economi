<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];
$id = (int) ($_GET['id'] ?? 0);
$error = '';

$stmt = $pdo->prepare("SELECT * FROM stock_portfolio WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$stock = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$stock) {
    header("Location: index.php");
    exit;
}

$initial_total = round((float) $stock['buy_price'] * (float) $stock['shares_qty'], 2);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stock_code = strtoupper(trim($_POST['stock_code'] ?? ''));
    $buy_price = (float) ($_POST['buy_price'] ?? 0);
    $total_amount = (float) ($_POST['total_amount'] ?? 0);
    $buy_date = $_POST['buy_date'] ?? date('Y-m-d');

    if ($stock_code === '' || $buy_price <= 0 || $total_amount <= 0) {
        $error = 'Kode aset, harga per lembar/unit, dan total nominal wajib diisi lebih dari 0.';
    } else {
        $shares_qty = $total_amount / $buy_price;
        $upd = $pdo->prepare("UPDATE stock_portfolio SET stock_code = ?, buy_price = ?, shares_qty = ?, buy_date = ? WHERE id = ? AND user_id = ?");
        $upd->execute([$stock_code, $buy_price, $shares_qty, $buy_date, $id, $user_id]);
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
    <title>Edit Saham - In This Economi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Edit Portofolio Saham &amp; Crypto</h1>
                </div>
                <p>Perbarui data pembelian aset dalam Rupiah (ketik angka polos tanpa titik).</p>
            </div>
        </div>

        <div class="card" style="max-width: 640px;">
            <form method="POST">
                <label for="stock_code">Kode Saham / Crypto:</label>
                <div style="display: flex; gap: 10px; align-items: stretch; margin-bottom: 18px;">
                    <input type="text" id="stock_code" name="stock_code" value="<?= htmlspecialchars($stock['stock_code']); ?>" style="flex: 1; min-width: 0; margin-bottom: 0;" required>
                    <button type="button" id="btnCekHarga" class="btn" style="background-color: var(--info); white-space: nowrap; flex-shrink: 0;">Cek Harga Live</button>
                </div>

                <label for="buy_price">Harga per Lembar / Unit (Rp):</label>
                <input type="number" id="buy_price" name="buy_price" step="any" min="0" value="<?= htmlspecialchars((string) $stock['buy_price']); ?>" required>

                <label for="total_amount">Total Nominal Uang yang Dikeluarkan (Rp):</label>
                <input type="number" id="total_amount" name="total_amount" step="any" min="0" value="<?= htmlspecialchars((string) $initial_total); ?>" required>

                <label for="shares_qty">Jumlah Unit / Lembar (Terhitung Otomatis):</label>
                <input type="number" id="shares_qty" name="shares_qty" step="any" value="<?= htmlspecialchars((string) $stock['shares_qty']); ?>" readonly style="background-color: var(--pink-soft); color: var(--pink-hover); font-weight: 700;">

                <label for="buy_date">Tanggal Pembelian:</label>
                <input type="date" id="buy_date" name="buy_date" value="<?= htmlspecialchars($stock['buy_date']); ?>" required>

                <div class="page-actions" style="margin-top: 10px;">
                    <button type="submit" class="btn">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/confirm_modal.php'; ?>

    <script>
        const stockCodeInput = document.getElementById('stock_code');
        const buyPriceInput = document.getElementById('buy_price');
        const totalAmountInput = document.getElementById('total_amount');
        const sharesQtyInput = document.getElementById('shares_qty');
        const btnCekHarga = document.getElementById('btnCekHarga');

        function hitungUnit() {
            const harga = parseFloat(buyPriceInput.value) || 0;
            const total = parseFloat(totalAmountInput.value) || 0;

            if (harga > 0 && total > 0) {
                const unit = total / harga;
                sharesQtyInput.value = Number.isInteger(unit) ? unit : parseFloat(unit.toFixed(6));
            } else {
                sharesQtyInput.value = 0;
            }
        }

        buyPriceInput.addEventListener('input', hitungUnit);
        totalAmountInput.addEventListener('input', hitungUnit);

        btnCekHarga.addEventListener('click', async () => {
            const rawCode = stockCodeInput.value.trim().toUpperCase();
            if (!rawCode) {
                window.showAppPopup({
                    type: 'info',
                    title: 'Kode Aset Kosong',
                    message: 'Masukkan kode saham atau crypto terlebih dahulu sebelum mengecek harga.'
                });
                return;
            }

            const originalText = btnCekHarga.textContent;
            btnCekHarga.disabled = true;
            btnCekHarga.textContent = 'Memuat...';

            try {
                const res = await fetch(`price.php?symbol=${encodeURIComponent(rawCode)}`);
                const data = await res.json();

                if (data && data.ok && typeof data.price === 'number') {
                    stockCodeInput.value = data.symbol || rawCode;
                    buyPriceInput.value = data.price;
                    hitungUnit();

                    const detailKurs = (data.original_currency && data.original_currency !== 'IDR')
                        ? ` (${data.original_currency} ${Number(data.original_price).toLocaleString('en-US')} × kurs ${Number(data.fx_rate).toLocaleString('id-ID')})`
                        : '';

                    window.showAppPopup({
                        type: 'info',
                        title: 'Harga Live Berhasil Dimuat',
                        message: `Harga ${data.symbol} saat ini: Rp ${Number(data.price).toLocaleString('id-ID')}${detailKurs}. Jumlah unit sudah dihitung ulang otomatis.`
                    });
                } else {
                    window.showAppPopup({
                        type: 'info',
                        title: 'Harga Otomatis Tidak Tersedia',
                        message: data?.message || 'Gagal memuat harga otomatis. Silakan ketik harga per lembar/unit secara manual.'
                    });
                }
            } catch (err) {
                window.showAppPopup({
                    type: 'info',
                    title: 'Koneksi Gagal',
                    message: 'Tidak dapat menghubungi server harga saat ini. Silakan isi harga secara manual.'
                });
            } finally {
                btnCekHarga.disabled = false;
                btnCekHarga.textContent = originalText;
            }
        });
    </script>

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
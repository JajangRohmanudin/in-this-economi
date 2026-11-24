<?php
require_once '../config/database.php';
checkLogin();

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM stock_portfolio WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT *, (buy_price * shares_qty) AS total_investment FROM stock_portfolio WHERE user_id = ? ORDER BY buy_date DESC, id DESC");
$stmt->execute([$user_id]);
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Saham - In This Economi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .ticker-badge {
            font-weight: 700;
            color: var(--pink-hover);
            transition: color 0.2s ease;
        }
        .ticker-badge.is-up {
            color: var(--success);
        }
        .ticker-badge.is-down {
            color: var(--danger);
        }
        .ticker-status {
            display: block;
            font-size: 0.75rem;
            color: var(--ink-muted);
            margin-top: 2px;
        }
        .ticker-status.is-up {
            color: var(--success);
            font-weight: 600;
        }
        .ticker-status.is-down {
            color: var(--danger);
            font-weight: 600;
        }
    </style>
</head>
<body class="app-page">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div class="header-title">
                <div class="header-title-row">
                    <button type="button" class="menu-toggle-btn js-open-sidebar">☰ Menu</button>
                    <h1>Portofolio Saham &amp; Crypto</h1>
                </div>
                <p>Pantau daftar instrumen investasi milikmu. Warna kode otomatis hijau saat naik dan merah saat turun dibanding harga beli.</p>
            </div>
            <div class="page-actions">
                <a href="add.php" class="btn">+ Tambah Saham</a>
            </div>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Harga Beli / Lembar</th>
                            <th>Unit Terhitung</th>
                            <th>Total Modal (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stocks)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--ink-muted); padding: 30px;">Belum ada portofolio saham tercatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stocks as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['buy_date']); ?></td>
                                    <td>
                                        <span class="ticker-badge js-live-ticker"
                                              data-symbol="<?= htmlspecialchars($s['stock_code'], ENT_QUOTES); ?>"
                                              data-buy-price="<?= htmlspecialchars((string) $s['buy_price'], ENT_QUOTES); ?>">
                                            <?= htmlspecialchars($s['stock_code']); ?>
                                        </span>
                                        <small class="ticker-status js-ticker-status">Mengecek harga...</small>
                                    </td>
                                    <td>Rp <?= number_format((float) $s['buy_price'], 0, ',', '.'); ?></td>
                                    <td><?= number_format((float) $s['shares_qty'], 4, ',', '.'); ?></td>
                                    <td><strong>Rp <?= number_format((float) $s['total_investment'], 0, ',', '.'); ?></strong></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="edit.php?id=<?= (int) $s['id']; ?>"
                                               class="action-pill action-pill-edit js-confirm-trigger"
                                               data-mode="edit"
                                               data-title="Konfirmasi Edit"
                                               data-message="Yakin ingin mengedit data aset <?= htmlspecialchars($s['stock_code'], ENT_QUOTES); ?>?"
                                               data-label="Ya, Edit">Edit</a>
                                            <a href="index.php?delete=<?= (int) $s['id']; ?>"
                                               class="action-pill action-pill-delete js-confirm-trigger"
                                               data-mode="delete"
                                               data-title="Konfirmasi Hapus"
                                               data-message="Yakin ingin menghapus data aset <?= htmlspecialchars($s['stock_code'], ENT_QUOTES); ?>?"
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

    <script>
        (async function () {
            const tickers = Array.from(document.querySelectorAll('.js-live-ticker'));
            if (!tickers.length) return;

            const uniqueSymbols = [...new Set(
                tickers.map(el => (el.dataset.symbol || '').trim().toUpperCase()).filter(Boolean)
            )];

            const priceMap = new Map();

            await Promise.all(uniqueSymbols.map(async (symbol) => {
                try {
                    const res = await fetch(`price.php?symbol=${encodeURIComponent(symbol)}`);
                    const data = await res.json();
                    if (data && data.ok && typeof data.price === 'number') {
                        priceMap.set(symbol, data.price);
                    }
                } catch (e) {
                    // Biarkan kosong jika gagal memuat harga live
                }
            }));

            tickers.forEach((badge) => {
                const symbol = (badge.dataset.symbol || '').trim().toUpperCase();
                const buyPrice = parseFloat(badge.dataset.buyPrice) || 0;
                const statusEl = badge.parentElement ? badge.parentElement.querySelector('.js-ticker-status') : null;
                const currentPrice = priceMap.get(symbol);

                badge.classList.remove('is-up', 'is-down');
                if (statusEl) {
                    statusEl.classList.remove('is-up', 'is-down');
                }

                if (typeof currentPrice !== 'number' || buyPrice <= 0) {
                    if (statusEl) {
                        statusEl.textContent = 'Harga live belum tersedia';
                    }
                    return;
                }

                const formattedLive = currentPrice.toLocaleString('id-ID');

                if (currentPrice > buyPrice) {
                    badge.classList.add('is-up');
                    if (statusEl) {
                        statusEl.classList.add('is-up');
                        statusEl.textContent = `▲ Naik (Rp ${formattedLive})`;
                    }
                } else if (currentPrice < buyPrice) {
                    badge.classList.add('is-down');
                    if (statusEl) {
                        statusEl.classList.add('is-down');
                        statusEl.textContent = `▼ Turun (Rp ${formattedLive})`;
                    }
                } else {
                    if (statusEl) {
                        statusEl.textContent = `= Stabil (Rp ${formattedLive})`;
                    }
                }
            });
        })();
    </script>

</body>
</html>
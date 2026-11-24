# In This Economi

Aplikasi web pencatat finansial harian, portofolio saham & crypto, serta manajemen operasional usaha (Owner Hub) berbasis **PHP**, **MySQL (PDO)**, **CSS**, dan **JavaScript**.

## Fitur Utama

1. **Keuangan Sehari-hari (`daily/`)**
   - Pencatatan arus kas masuk (`in`) dan keluar (`out`) berdasarkan akun pengguna.
   - Pengelolaan kategori, nominal, catatan, dan tanggal transaksi (CRUD).

2. **Portofolio Saham & Crypto (`stocks/`)**
   - Pengecekan harga live (`stocks/price.php`) dengan konversi otomatis mata uang non-IDR ke **IDR**.
   - Perhitungan otomatis jumlah unit/lembar berdasarkan total nominal uang yang dikeluarkan dan harga per unit.
   - Indikator visual naik (hijau) dan turun (merah) pada daftar portofolio dengan membandingkan harga live terhadap harga beli.

3. **Owner Hub — Manajemen Usaha (`owner/`)**
   - Pencatatan penjualan produk (`sales*.php`) dan pengeluaran usaha (`expenses*.php`), seperti bahan baku, gaji/borongan, operasional, dan pemasaran.
   - Perhitungan otomatis **Omzet**, **Modal Bahan Baku**, **Laba Kotor (Gross Profit)**, dan **Laba Bersih (Net Profit)**.

4. **Tampilan & Navigasi**
   - Gaya *warm ivory* & *soft pink* dengan tipografi *Playfair Display* dan *Plus Jakarta Sans*.
   - Area konten *full-width* dengan *sidebar drawer* tersembunyi yang dapat dibuka lewat tombol menu serta tata letak yang responsif untuk layar *mobile*.

## Struktur Folder (Ringkas)

```text
in-this-economi/
├── assets/
│   └── css/style.css
├── auth/
├── config/
│   └── database.php
├── daily/
├── includes/
│   ├── sidebar.php
│   └── confirm_modal.php
├── owner/
├── stocks/
│   └── price.php
├── dashboard.php
├── index.php
└── README.md
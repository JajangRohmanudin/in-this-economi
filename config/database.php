<?php
// Nyalakan error reporting untuk pelacakan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Mulai sesi jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'db_in_this_economi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// ==========================================
// FUNGSI CEK LOGIN (WAJIB ADA DI SINI)
// ==========================================
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        // Arahkan ke halaman login di dalam folder auth
        header("Location: /in-this-economi/auth/login.php");
        exit;
    }
}
?>
<?php
require_once '../config/database.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = "Username sudah digunakan, silakan pilih yang lain!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($insert->execute([$username, $hashed_password])) {
                $success = "Registrasi berhasil! Silakan <a href='login.php'>Login di sini</a>.";
            } else {
                $error = "Terjadi kesalahan saat mendaftar.";
            }
        }
    } else {
        $error = "Username dan password tidak boleh kosong!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - In This Economi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f8fafc; margin: 0; }
        .auth-card { width: 100%; max-width: 400px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    </style>
    <script>
        function togglePassword() {
            let passwordInput = document.getElementById('password');
            let toggleBtn = document.getElementById('toggle-btn');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.innerText = '🙈 Sembunyikan';
            } else {
                passwordInput.type = 'password';
                toggleBtn.innerText = '👁️ Lihat';
            }
        }
    </script>
</head>
<body class="app-page">

    <div class="auth-card">
        <h2 style="color: var(--primary-color); margin-bottom: 5px; text-align: center;">Daftar Akun Baru</h2>
        <p style="text-align: center; color: #64748b; margin-bottom: 25px; font-size: 0.9rem;">Kelola keuangan dan bisnismu secara privat.</p>

        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.85rem;"><?= $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.85rem;"><?= $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label>Username:</label>
            <input type="text" name="username" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">

            <label>Password:</label>
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; margin-bottom: 0;">
                <button type="button" id="toggle-btn" onclick="togglePassword()" style="padding: 0 15px; background: #e2e8f0; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; font-size: 0.85rem; white-space: nowrap;">👁️ Lihat</button>
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 12px;">Daftar Sekarang</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #64748b;">
            Sudah punya akun? <a href="login.php" style="color: #2563eb; text-decoration: none; font-weight: 500;">Login di sini</a>
        </p>
    </div>

</body>
</html>
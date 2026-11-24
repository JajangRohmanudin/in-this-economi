<?php
require_once '../config/database.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            header("Location: ../index.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Harap isi username dan password!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - In This Economi</title>
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
        <h2 style="color: var(--primary-color); margin-bottom: 5px; text-align: center;">Masuk Aplikasi</h2>
        <p style="text-align: center; color: #64748b; margin-bottom: 25px; font-size: 0.9rem;">In This Economi - Portofolio Finansial</p>

        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.85rem;"><?= $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label>Username:</label>
            <input type="text" name="username" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">

            <label>Password:</label>
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; margin-bottom: 0;">
                <button type="button" id="toggle-btn" onclick="togglePassword()" style="padding: 0 15px; background: #e2e8f0; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; font-size: 0.85rem; white-space: nowrap;">👁️ Lihat</button>
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 12px;">Masuk</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #64748b;">
            Belum punya akun? <a href="register.php" style="color: #2563eb; text-decoration: none; font-weight: 500;">Daftar di sini</a>
        </p>
    </div>

</body>
</html>
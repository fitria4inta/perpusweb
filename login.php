<?php
session_start();
if (!empty($_SESSION['petugas_login'])) { header('Location: index.php'); exit; }

// Satu akun petugas. Password disimpan dalam bentuk hash.
$USERNAME = 'petugas';
$PASSWORD_HASH = '$2y$12$q/7.EuJ888VkF2UueQyB2eXYsf5V3NevdzP80oUa53kNz8MfgR2ZW';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (hash_equals($USERNAME, $username) && password_verify($password, $PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['petugas_login'] = true;
        $_SESSION['petugas_nama'] = 'Petugas';
        header('Location: index.php');
        exit;
    }
    $error = 'Username atau password yang kamu masukkan salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Petugas — PerpusKu</title><link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<main class="login-card">
  <div class="login-brand"><span class="brand-mark">P</span><div><b>Perpus<span>Ku</span></b><small>LIBRARY MANAGEMENT</small></div></div>
  <div class="login-heading"><h1>Selamat Datang</h1><p>Silakan masuk untuk mengelola perpustakaan.</p></div>
  <?php if ($error): ?><div class="login-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <form method="post" autocomplete="on">
    <label for="username">Username</label>
    <input id="username" name="username" type="text" placeholder="Masukkan username" required autofocus autocomplete="username">
    <label for="password">Password</label>
    <div class="password-wrap"><input id="password" name="password" type="password" placeholder="Masukkan password" required autocomplete="current-password"><button type="button" id="togglePassword" aria-label="Tampilkan password">Lihat</button></div>
    <button class="login-submit" type="submit">Masuk</button>
  </form>
  <p class="login-foot">© 2026 PerpusKu · Sistem Informasi Perpustakaan</p>
</main>
<script>
document.getElementById('togglePassword').addEventListener('click', function () {
  const input = document.getElementById('password');
  const visible = input.type === 'password';
  input.type = visible ? 'text' : 'password';
  this.textContent = visible ? 'Sembunyi' : 'Lihat';
  this.setAttribute('aria-label', visible ? 'Sembunyikan password' : 'Tampilkan password');
});
</script>
</body></html>

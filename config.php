<?php
// Sesuaikan jika konfigurasi MySQL Laragon Anda berbeda.
$host = '127.0.0.1';
$db   = 'db_perpustakaan';
$user = 'root';
$pass = ''; // Default Laragon biasanya kosong
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
];
try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  http_response_code(500);
  die('Koneksi database gagal. Periksa config.php dan pastikan MySQL Laragon aktif. Detail: ' . htmlspecialchars($e->getMessage()));
}

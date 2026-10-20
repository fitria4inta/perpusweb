<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Perpustakaan — Dashboard</title><link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app">
<aside class="sidebar" id="sidebar">
  <div class="brand"><span class="brand-mark">P</span><div><b>Perpus<span>Ku</span></b><small>LIBRARY MANAGEMENT</small></div></div>
  <div class="nav-label">MENU UTAMA</div>
  <button class="nav active" data-page="dashboard"><span>▦</span> Dashboard</button>
  <button class="nav" data-page="books"><span>▤</span> Data Buku</button>
  <button class="nav" data-page="members"><span>♙</span> Data Anggota</button>
  <button class="nav" data-page="loans"><span>⇄</span> Peminjaman</button>
  <button class="nav" data-page="history"><span>◷</span> Riwayat Transaksi</button>
  <div class="side-bottom"><div class="avatar">A</div><div><b>Administrator</b><small>Pengelola Perpustakaan</small></div></div>
</aside>
<main class="main">
  <header class="topbar"><button class="menu-btn" id="menuBtn">☰</button><div><small id="crumb">HALAMAN UTAMA</small><h1 id="pageTitle">Dashboard</h1></div><div class="top-right"><span class="today" id="today"></span><div class="avatar">A</div></div></header>
  <section id="content" class="content"></section>
  <footer>© 2026 PerpusKu · Sistem Informasi Perpustakaan</footer>
</main>
</div>
<div class="modal-backdrop" id="modalBackdrop"><div class="modal"><div class="modal-head"><h2 id="modalTitle">Tambah data</h2><button class="icon-btn" id="closeModal">×</button></div><form id="dataForm"><div id="formFields" class="form-grid"></div><div class="modal-actions"><button type="button" class="btn light" id="cancelModal">Batal</button><button class="btn primary" type="submit">Simpan Data</button></div></form></div></div>
<div id="toast" class="toast"></div>
<script src="app.js"></script>
</body></html>

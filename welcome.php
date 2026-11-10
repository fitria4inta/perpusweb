
<?php
session_start();

if (empty($_SESSION['petugas_login'])) {
    header('Location: login.php');
    exit;
}

$nama = $_SESSION['petugas_nama'] ?? 'Petugas';
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Welcome - PerpusKu</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #7da7f5, #3568a8);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 35px;
}

/* CONTAINER UTAMA */
.landing-container {
    width: 100%;
    max-width: 1250px;
    min-height: 620px;
    background: white;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 20px 45px rgba(0,0,0,0.18);
}

/* NAVBAR */
.navbar {
    height: 85px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 55px;
    position: relative;
    z-index: 2;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #142746;
    font-size: 25px;
    font-weight: 800;
}

.logo-icon {
    width: 40px;
    height: 40px;
    background: #142746;
    color: white;
    display: grid;
    place-items: center;
    border-radius: 9px;
    font-size: 25px;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 30px;
}

.nav-links a {
    text-decoration: none;
    color: #53627a;
    font-size: 13px;
    font-weight: 600;
    transition: 0.3s;
}

.nav-links a:hover {
    color: #3f6fba;
}

.nav-links .contact {
    background: #3f6fba;
    color: white;
    padding: 11px 20px;
    border-radius: 25px;
}

/* HERO */
.hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 535px;
    align-items: center;
}

/* BAGIAN TEKS */
.hero-content {
    padding: 35px 0 55px 55px;
    position: relative;
    z-index: 1;
}

.hero-label {
    color: #3f6fba;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 2px;
    margin-bottom: 20px;
}

.hero-content h1 {
    color: #142746;
    font-size: clamp(34px, 4vw, 48px);
    line-height: 1.17;
    margin-bottom: 20px;
    letter-spacing: -1.5px;
}

.hero-content h1 span {
    color: #3f6fba;
}

.hero-description {
    color: #7b8798;
    font-size: 14px;
    line-height: 1.9;
    max-width: 440px;
    margin-bottom: 25px;
}

.user-greeting {
    color: #526078;
    font-size: 14px;
    margin-bottom: 25px;
}

.features {
    display: flex;
    flex-wrap: wrap;
    gap: 9px;
    margin-bottom: 32px;
}

.features span {
    background: #edf3ff;
    color: #25416b;
    padding: 9px 15px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 600;
}

.btn-start {
    display: inline-block;
    background: #3f6fba;
    color: white;
    text-decoration: none;
    padding: 14px 27px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 5px 15px rgba(63,111,186,0.25);
    transition: 0.3s;
}

.btn-start:hover {
    background: #142746;
    transform: translateY(-3px);
}

/* BAGIAN GAMBAR */
.hero-image {
    height: 100%;
    min-height: 480px;
    overflow: hidden;
    position: relative;
    background: #142746;
    border-radius: 50px 0 0 50px;
}

.hero-image img {
    width: 200%;
    height: 100%;
    max-width: none;
    position: absolute;
    top: 0;
    right: 0;
    object-fit: cover;
    object-position: right center;
}

/* FOOTER */
.footer {
    padding: 15px 55px 25px;
    color: #9aa4b2;
    font-size: 11px;
}

/* RESPONSIVE TABLET */
@media (max-width: 900px) {
    body {
        padding: 20px;
    }

    .navbar {
        padding: 0 30px;
    }

    .hero {
        grid-template-columns: 1fr;
    }

    .hero-content {
        padding: 40px 35px;
    }

    .hero-image {
        min-height: 350px;
        border-radius: 0;
    }

    .footer {
        padding: 20px 30px;
    }
}

/* RESPONSIVE HP */
@media (max-width: 550px) {
    body {
        padding: 12px;
    }

    .landing-container {
        border-radius: 15px;
    }

    .navbar {
        height: 75px;
        padding: 0 20px;
    }

    .logo {
        font-size: 21px;
    }

    .nav-links {
        gap: 10px;
    }

    .nav-links a {
        font-size: 11px;
    }

    .nav-links a:first-child {
        display: none;
    }

    .nav-links .contact {
        padding: 9px 12px;
    }

    .hero-content {
        padding: 35px 23px;
    }

    .hero-content h1 {
        font-size: 34px;
    }

    .hero-description {
        font-size: 13px;
    }

    .hero-image {
        min-height: 260px;
    }

    .footer {
        padding: 18px 23px;
    }
}
</style>
</head>

<body>

<div class="landing-container">

    <!-- NAVBAR -->
    <nav class="navbar">

        <div class="logo">
            <div class="logo-icon">P</div>
            PerpusKu
        </div>

        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="index.php">Dashboard</a>
            <a href="logout.php" class="contact">Logout</a>
        </div>

    </nav>


    <!-- HERO SECTION -->
    <section class="hero" id="home">

        <!-- BAGIAN KIRI -->
        <div class="hero-content">

            <div class="hero-label">
                LIBRARY MANAGEMENT SYSTEM
            </div>

            <h1>
                Kelola Perpustakaan Jadi Lebih Mudah
                <span>dan Terorganisir.</span>
            </h1>

            <p class="user-greeting">
                Selamat datang kembali,
                <strong><?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>!</strong>
            </p>

            <p class="hero-description">
                Solusi digital untuk membantu pengelolaan
                perpustakaan secara lebih praktis, efisien,
                dan terstruktur. Kelola data buku, anggota,
                serta transaksi perpustakaan dalam satu platform.
            </p>

            <div class="features">
                <span>Data Buku</span>
                <span>Data Anggota</span>
                <span>Peminjaman</span>
                <span>Pengembalian</span>
            </div>

            <a href="index.php" class="btn-start">
                Mulai Kelola →
            </a>

        </div>


        <!-- BAGIAN KANAN -->
        <div class="hero-image">

            <img
                src="assets/perpusku-preview.png"
                alt="Tampilan Dashboard PerpusKu"
            >

        </div>

    </section>


    <!-- FOOTER -->
    <footer class="footer">
        © 2026 PerpusKu · Library Management System
    </footer>

</div>

</body>
</html>

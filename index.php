<?php
include 'config/db.php';
$berita = $conn->query("SELECT * FROM berita ORDER BY tanggal DESC");
$sampah = $conn->query("SELECT * FROM sampah ORDER BY nama_sampah ASC");
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bank Sampah - Selamatkan Lingkungan</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link href="assets/css/index.css" rel="stylesheet">
    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                    <i class="bi bi-recycle me-2"></i> 
                    <span>Bank Sampah</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link rounded mx-1 position-relative" href="index.php">
                                <span>Home</span>
                                <span class="position-absolute bottom-0 start-50 translate-middle-x bg-white hover-underline" style="height: 2px; width: 0; transition: width 0.3s ease;"></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded mx-1 position-relative" href="auth/login.php">
                                <span>Login</span>
                                <span class="position-absolute bottom-0 start-50 translate-middle-x bg-white hover-underline" style="height: 2px; width: 0; transition: width 0.3s ease;"></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link rounded mx-1 position-relative" href="auth/signup.php">
                                <span>Sign Up</span>
                                <span class="position-absolute bottom-0 start-50 translate-middle-x bg-white hover-underline" style="height: 2px; width: 0; transition: width 0.3s ease;"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <section class="hero position-relative">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="assets/img/1.jpg" class="ds-block w-100" alt="Hero 1">
                    </div>
                    <div class="carousel-item">
                        <img src="assets/img/2.jpg" class="d-block w-100" alt="Hero 2">
                    </div>
                    <div class="carousel-item">
                        <img src="assets/img/3.jpg" class="d-block w-100" alt="Hero 3">
                    </div>
                    <div class="carousel-item">
                        <img src="assets/img/4.jpg" class="d-block w-100" alt="Hero 3">
                    </div>
                    <div class="carousel-item">
                        <img src="assets/img/5.jpg" class="d-block w-100" alt="Hero 3">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <div class="carousel-fixed-caption text-center">
                    <h1 class="display-5 fw-bold">Tukar Sampah Jadi Uang</h1>
                    <p class="lead">Kami membantu Anda menukar sampah daur ulang menjadi uang tunai!</p>
                    <a href="auth/signup.php" class="btn btn-success btn-lg mt-3">Gabung Sekarang</a>
                </div>
            </div>
        </section>

        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-4">Layanan Kami</h2>
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="card shadow-sm mb-4 text-center">
                            <div class="card-body">
                                <i class="bi bi-truck fs-1 text-success"></i>
                                <h5 class="mt-3">Penjemputan Sampah</h5>
                                <p>Kami menjemput sampah Anda langsung ke rumah.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm mb-4 text-center">
                            <div class="card-body">
                                <i class="bi bi-cash-coin fs-1 text-success"></i>
                                <h5 class="mt-3">Tukar Sampah Jadi Uang</h5>
                                <p>Sampah Anda akan dibayar sesuai harga pasar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">Daftar Harga Sampah</h2>
                <div class="position-relative">
                    <div class="scrolling-wrapper">
                        <?php while ($s = $sampah->fetch_assoc()): ?>
                            <div class="scrolling-card">
                                <div class="card h-100">
                                    <?php if (!empty($s['gambar']) && file_exists("uploads/{$s['gambar']}")): ?>
                                        <img src="uploads/<?= htmlspecialchars($s['gambar']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="assets/gambar/default.jpg" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($s['nama_sampah']) ?></h5>
                                        <p class="card-text">Harga: Rp<?= number_format($s['harga'], 2, ',', '.') ?> / kg</p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-4">Berita Terkini</h2>
                <div class="position-relative">
                    <div class="scrolling-wrapper">
                        <?php while ($b = $berita->fetch_assoc()): ?>
                            <div class="scrolling-card">
                                <div class="card h-100">
                                    <?php if (!empty($b['gambar']) && file_exists("uploads/{$b['gambar']}")): ?>
                                        <img src="uploads/<?= htmlspecialchars($b['gambar']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="assets/gambar/default.jpg" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= htmlspecialchars($b['judul']) ?></h5>
                                        <p class="card-text"><small class="text-muted"><?= date('d M Y', strtotime($b['tanggal'])) ?></small></p>
                                        <div class="card-text-container flex-grow-1">
                                            <?php 
                                            $isi_berita = htmlspecialchars($b['isi']);
                                            $preview = substr($isi_berita, 0, 150);
                                            echo '<p class="card-text-preview">' . $preview . '</p>';
                                            if (strlen($isi_berita) > 150): ?>
                                                <span class="card-text-more"><?= substr($isi_berita, 150) ?></span>
                                                <span class="read-more-btn" onclick="toggleReadMore(this)">Baca selengkapnya</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">Tentang Kami</h2>
                <p class="text-center">Bank Sampah adalah platform untuk menukar sampah menjadi uang sekaligus menjaga lingkungan.</p>
            </div>
        </section>

        <footer class="footer bg-success text-white py-3">
            <div class="container text-center">
                <p>&copy; 2025 Bank Sampah. All rights reserved.</p>
                <p><i class="bi bi-instagram me-2"></i> @banksampah | <i class="bi bi-whatsapp mx-2"></i> 0812-4383-4669</p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/index.js"></script>
    </body>
</html>
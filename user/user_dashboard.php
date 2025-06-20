<?php
include '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('user');

$id_user = intval($_SESSION['user_id']);
$username = htmlspecialchars($_SESSION['username'] ?? 'User');

$stmtTransaksi = $conn->prepare("SELECT COUNT(*) AS total_transaksi FROM transaksi WHERE id_user = ? AND status = 'diterima'");
$stmtTransaksi->bind_param("i", $id_user);
$stmtTransaksi->execute();
$resultTransaksi = $stmtTransaksi->get_result();
$dataTransaksi = $resultTransaksi->fetch_assoc();
$stmtTransaksi->close();

$stmtSampah = $conn->prepare("SELECT SUM(jumlah) AS total_kg FROM transaksi WHERE status = 'Diterima' AND id_user = ?");
$stmtSampah->bind_param("i", $id_user);
$stmtSampah->execute();
$dataSampah = $stmtSampah->get_result()->fetch_assoc();
$stmtSampah->close();

$stmtPendapatan = $conn->prepare("SELECT SUM(jumlah * harga) AS total_pendapatan FROM transaksi WHERE status = 'Diterima' AND id_user = ?");
$stmtPendapatan->bind_param("i", $id_user);
$stmtPendapatan->execute();
$dataPendapatan = $stmtPendapatan->get_result()->fetch_assoc();
$stmtPendapatan->close();

$resultSampahList = $conn->query("SELECT * FROM sampah ORDER BY nama_sampah ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - User Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../assets/css/masyarakat.css" rel="stylesheet" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">User Bank Sampah</a>
            <div class="d-flex">
                <a href="../auth/logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>
    <div class="custom-sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="user_dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark px-3 py-2" href="user_transaksi.php">
                    <i class="bi bi-cash-coin me-2"></i> Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark px-3 py-2" href="user_penjemputan.php">
                    <i class="bi bi-truck me-2"></i> Penjemputan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark px-3 py-2" href="user_profile.php">
                    <i class="bi bi-person-circle me-2"></i> Profil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark px-3 py-2" href="user_feedback.php">
                    <i class="bi bi-chat-square-text me-2"></i> Feedback
                </a>
            </li>
        </ul>
    </div>
    <main class="main-content">
        <h2 class="mb-4">Dashboard User</h2>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Jumlah Sampah Terkumpul</h5>
                        <p class="fs-3"><?= $dataSampah['total_kg'] ?? 0; ?> Kg</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Pendapatan</h5>
                        <p class="fs-3">Rp <?= number_format($dataPendapatan['total_pendapatan'] ?? 0, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Jumlah Transaksi User</h5>
                        <p class="fs-3"><?= $dataTransaksi['total_transaksi'] ?? 0; ?> Transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        <section class="py-3">
            <h4 class="mb-4">Daftar Harga Sampah</h4>
            <div class="scrolling-wrapper d-flex overflow-auto pb-3">
                <?php while ($s = mysqli_fetch_assoc($resultSampahList)) : ?>
                    <div class="card me-3" style="min-width: 250px; flex: 0 0 auto;">
                        <?php
                        $gambarPath = (!empty($s['gambar']) && file_exists("../uploads/" . $s['gambar']))
                            ? "../uploads/" . htmlspecialchars($s['gambar'])
                            : "../assets/gambar/default.jpg";
                        ?>
                        <img src="<?= $gambarPath ?>" class="card-img-top" alt="<?= htmlspecialchars($s['nama_sampah']) ?>" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($s['nama_sampah']) ?></h5>
                            <p class="card-text">Harga: Rp <?= number_format($s['harga'], 0, ',', '.') ?> / kg</p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

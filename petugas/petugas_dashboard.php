<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

$id_petugas = $_SESSION['user_id']; 

$stmtUser = $conn->prepare("SELECT COUNT(*) AS total_user FROM user");
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$dataUser = $resultUser->fetch_assoc();

$stmtSampah = $conn->prepare("SELECT SUM(jumlah) AS total_sampah FROM transaksi WHERE status='Diterima' AND id_petugas = ?");
$stmtSampah->bind_param("i", $id_petugas);
$stmtSampah->execute();
$resultSampah = $stmtSampah->get_result();
$dataSampah = $resultSampah->fetch_assoc();

$stmtPenjemputan = $conn->prepare("SELECT COUNT(*) AS total_menunggu FROM penjemputan WHERE status = 'Menunggu'");
$stmtPenjemputan->execute();
$resultPenjemputan = $stmtPenjemputan->get_result();
$dataPenjemputan = $resultPenjemputan->fetch_assoc();

$stmtGrafik = $conn->prepare("SELECT tanggal, SUM(jumlah) AS total FROM transaksi WHERE status='Diterima' AND id_petugas = ? GROUP BY tanggal ORDER BY tanggal ASC");
$stmtGrafik->bind_param("i", $id_petugas); 
$stmtGrafik->execute();
$resultGrafik = $stmtGrafik->get_result();

$grafikLabels = [];
$grafikData = [];

while ($row = $resultGrafik->fetch_assoc()) {
    $grafikLabels[] = $row['tanggal'];
    $grafikData[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Dashboard Petugas - Bank Sampah</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link href="../assets/css/petugas_dashboard.css" rel="stylesheet">
    </head>

    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Petugas Bank Sampah</a>
                <div class="d-flex">
                    <a href="../auth/logout.php" class="btn btn-light">Logout</a>
                </div>
            </div>
        </nav>

        <div class="custom-sidebar">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="petugas_dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark px-3 py-2" href="petugas_user.php">
                        <i class="bi bi-people me-2"></i> Data User
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark px-3 py-2" href="petugas_penjemputan.php">
                        <i class="bi bi-truck me-2"></i> Penjemputan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark px-3 py-2" href="petugas_transaksi.php">
                        <i class="bi bi-cash-coin me-2"></i> Transaksi
                    </a>
                </li>
            </ul>
        </div>

        <main class="main-content">
            <h2 class="mb-4">Dashboard Petugas</h2>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Jumlah User</h5>
                            <p class="display-6 fw-bold"><?= $dataUser['total_user'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">Sampah Terkumpul</h5>
                            <p class="display-6 fw-bold"><?= number_format($dataSampah['total_sampah'] ?? 0, 2) ?> Kg</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Permintaan Penjemputan</h5>
                            <p class="display-6 fw-bold"><?= $dataPenjemputan['total_penjemputan'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card">
                <div class="card-header bg-success text-white">Grafik Sampah Diterima</div>
                <div class="card-body">
                    <div style="position: relative; width: 100%; height: 400px;">
                    <canvas id="grafikSampahPetugas"></canvas>
                    </div>
                </div>
            </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?= json_encode($grafikLabels); ?>;
        const dataSampah = <?= json_encode($grafikData); ?>;

        const ctx = document.getElementById('grafikSampahPetugas').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sampah Diterima (kg)',
                    data: dataSampah,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,              // Titik pada grafik
                    pointHoverRadius: 6          // Titik yang lebih besar saat hover
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,      // Menjaga aspek rasio grafik
                layout: {
                    padding: {
                        right: 30               // Padding kanan
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,      // Rotasi label sumbu X
                            minRotation: 30       // Rotasi label sumbu X
                        },
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Kilogram (kg)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true
                    }
                }
            }
        });
    </script>
    </body>

</html>

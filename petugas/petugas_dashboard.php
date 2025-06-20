<?php
require_once '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('petugas');

$id_petugas = $_SESSION['user_id']; 

$stmtTransaksi = $conn->prepare("SELECT COUNT(*) AS total_transaski FROM transaksi WHERE id_petugas = ? AND status = 'diterima'");
$stmtTransaksi->bind_param("i", $id_petugas);
$stmtTransaksi->execute();
$resultPetugas = $stmtTransaksi->get_result();
$dataTransaksi = $resultPetugas->fetch_assoc();
$stmtTransaksi->close();

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
        <link href="../assets/css/petugas.css" rel="stylesheet">
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
                            <h5 class="card-title">Jumlah Transaksi</h5>
                            <p class="display-6 fw-bold"><?= $dataTransaksi['total_transaski'] ?? 0; ?></p>
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
                            <p class="display-6 fw-bold"><?= $dataPenjemputan['total_menunggu'] ?? 0; ?></p>
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
            // Mengkonversi array PHP ke format JavaScript untuk Chart.js
            const labels = <?= json_encode($grafikLabels); ?>;       // Label sumbu X (tanggal/waktu)
            const dataSampah = <?= json_encode($grafikData); ?>;     // Data sumbu Y (jumlah sampah dalam kg)

            // Mengambil elemen canvas tempat grafik akan ditampilkan
            const ctx = document.getElementById('grafikSampahPetugas').getContext('2d');
            
            // Membuat instance Chart baru dengan konfigurasi
            const chart = new Chart(ctx, {
                type: 'line',  // Mengatur jenis grafik menjadi garis
                data: {
                    labels: labels,  // Mengatur label sumbu X dari data PHP
                    datasets: [{
                        label: 'Sampah Diterima (kg)',  // Label untuk legenda
                        data: dataSampah,               // Nilai sumbu Y dari data PHP
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',  // Warna hijau untuk area dengan opacity 20%
                        borderColor: 'rgba(40, 167, 69, 1)',       // Warna hijau solid untuk garis
                        borderWidth: 2,                 // Ketebalan garis
                        tension: 0.3,                  // Kelengkungan garis (0-1)
                        fill: true,                     // Mengisi area di bawah garis
                        pointRadius: 4,                 // Ukuran titik data
                        pointHoverRadius: 6             // Ukuran titik saat dihover
                    }]
                },
                options: {
                    responsive: true,                   // Grafik menyesuaikan ukuran container
                    maintainAspectRatio: false,         // Memungkinkan dimensi custom
                    layout: {
                        padding: {
                            right: 30                  // Menambahkan ruang di sisi kanan
                        }
                    },
                    scales: {
                        x: {  // Konfigurasi sumbu X
                            ticks: {
                                autoSkip: false,       // Menampilkan semua label (tanpa skip)
                                maxRotation: 45,       // Sudut kemiringan maksimum label
                                minRotation: 30        // Sudut kemiringan minimum label
                            },
                            title: {
                                display: true,        // Menampilkan judul sumbu
                                text: 'Tanggal'       // Judul sumbu X
                            }
                        },
                        y: {  // Konfigurasi sumbu Y
                            beginAtZero: true,        // Memulai skala dari 0 kg
                            title: {
                                display: true,        // Menampilkan judul sumbu
                                text: 'Kilogram (kg)' // Judul sumbu Y
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'           // Posisi legenda di atas grafik
                        },
                        title: {
                            display: true            // Mengaktifkan judul grafik (meski belum didefinisikan)
                        }
                    }
                }
            });
        </script>
    </body>

</html>

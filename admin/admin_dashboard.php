<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$stmtUser = $conn->prepare("SELECT COUNT(*) AS total_user FROM user");
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$dataUser = $resultUser->fetch_assoc();

$stmtPetugas = $conn->prepare("SELECT COUNT(*) AS total_petugas FROM petugas");
$stmtPetugas->execute();
$resultPetugas = $stmtPetugas->get_result();
$dataPetugas = $resultPetugas->fetch_assoc();

$stmtSampah = $conn->prepare("SELECT SUM(jumlah) AS total_sampah FROM transaksi WHERE status = 'Diterima'");
$stmtSampah->execute();
$resultSampah = $stmtSampah->get_result();
$dataSampah = $resultSampah->fetch_assoc();

$stmtGrafik = $conn->prepare("SELECT tanggal, SUM(jumlah) AS total FROM transaksi WHERE status = 'Diterima' GROUP BY tanggal ORDER BY tanggal ASC");
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
  <title>Admin Dashboard - Bank Sampah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/admin_dashboard.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Admin Bank Sampah</a>
      <div class="d-flex">
        <a href="../auth/logout.php" class="btn btn-light">Logout</a>
      </div>
    </div>
  </nav>

  <div class="custom-sidebar">
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="admin_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
      <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_user.php"><i class="bi bi-people me-2"></i> Data User</a></li>
      <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_petugas.php"><i class="bi bi-person-badge me-2"></i> Data Petugas</a></li>
      <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_sampah.php"><i class="bi bi-trash me-2"></i> Data Sampah</a></li>
      <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_berita.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a></li>
    </ul>
  </div>

  <main class="main-content">
    <h1 class="mb-4">Dashboard Admin</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Jumlah User</h5>
                    <p class="card-text fs-4"><?= $dataUser['total_user'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Petugas</h5>
                   <p class="card-text fs-4"><?= $dataPetugas['total_petugas'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Sampah Terkumpul</h5>
                    <p class="card-text fs-4"><?= number_format($dataSampah['total_sampah'] ?? 0, 2) ?> Kg</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
      <div class="card-header bg-success text-white">Grafik Sampah Diterima</div>
      <div class="card-body">
        <div style="position: relative; width: 100%; height: 400px;">
          <canvas id="sampahChart"></canvas>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('sampahChart').getContext('2d');
    const sampahChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($grafikLabels) ?>,
        datasets: [{
          label: 'Sampah Diterima (Kg)',
          data: <?= json_encode($grafikData) ?>,
          borderColor: 'green',
          backgroundColor: 'rgba(0, 128, 0, 0.2)',
          tension: 0.4,
          fill: true,
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: {
            right: 30
          }
        },
        scales: {
          x: {
            ticks: {
              autoSkip: false,
              maxRotation: 45,
              minRotation: 30
            }
          },
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            position: 'top'
          }
        }
      }
    });
  </script>
</body>
</html>
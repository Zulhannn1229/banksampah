<?php
require_once '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('user');

$username = $_SESSION['username'] ?? 'User';

$queryUser = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
$queryUser->bind_param("s", $username);
$queryUser->execute();
$resultUser = $queryUser->get_result();
$dataUser = $resultUser->fetch_assoc();
$id_user = $dataUser['id_user'] ?? 0;

if (isset($_GET['aksi'], $_GET['id']) && is_numeric($_GET['id'])) {
    $aksi = $_GET['aksi'];
    $id_transaksi = intval($_GET['id']);

    if ($aksi === 'terima') {
        $update = $conn->prepare("UPDATE transaksi SET status = 'diterima' WHERE id_transaksi = ? AND id_user = ?");
        $update->bind_param("ii", $id_transaksi, $id_user);
        $update->execute();
        $_SESSION['flash'] = "Transaksi berhasil diterima.";
    } elseif ($aksi === 'tolak') {
        $update = $conn->prepare("UPDATE transaksi SET status = 'ditolak' WHERE id_transaksi = ? AND id_user = ?");
        $update->bind_param("ii", $id_transaksi, $id_user);
        $update->execute();
        $_SESSION['flash'] = "Transaksi berhasil ditolak.";
    }
    header("Location: user_transaksi.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Transaksi - User Bank Sampah</title>
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
                    <a class="nav-link text-dark px-3 py-2" href="user_dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="user_transaksi.php">
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
            <h2 class="mb-4">Ajuan Transaksi dari Petugas</h2>

            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Nama Petugas</th>
                                <th>Jenis Sampah</th>
                                <th>Berat (Kg)</th>
                                <th>Harga Total</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT t.*, p.nama_petugas, (t.harga * t.jumlah) AS total_harga
                                FROM transaksi t
                                JOIN petugas p ON t.id_petugas = p.id_petugas
                                WHERE t.id_user = ? AND t.status = 'menunggu'";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $id_user);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>TRX" . str_pad($row['id_transaksi'], 3, '0', STR_PAD_LEFT) . "</td>
                                    <td>{$row['nama_petugas']}</td>
                                    <td>{$row['nama_sampah']}</td>
                                    <td>{$row['jumlah']}</td>
                                    <td>Rp" . number_format($row['total_harga'], 0, ',', '.') . "</td>
                                    <td>" . date('d-m-Y H:i', strtotime($row['tanggal'])) . "</td>
                                    <td>
                                        <a href='?aksi=terima&id={$row['id_transaksi']}' class='btn btn-success btn-sm' onclick='return confirm(\"Terima transaksi ini?\")'>Terima</a>
                                        <a href='?aksi=tolak&id={$row['id_transaksi']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Tolak transaksi ini?\")'>Tolak</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>Tidak ada ajuan transaksi.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <h4 class="mb-3">Riwayat Transaksi</h4>
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Nama Petugas</th>
                                <th>Jenis Sampah</th>
                                <th>Berat (Kg)</th>
                                <th>Harga Total</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql = "SELECT t.*, p.nama_petugas, (t.harga * t.jumlah) AS total_harga
                                FROM transaksi t
                                JOIN petugas p ON t.id_petugas = p.id_petugas
                                WHERE t.id_user = ? AND t.status IN ('diterima', 'ditolak')";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $id_user);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $badge = $row['status'] === 'diterima' ? 'success' : 'danger';
                                echo "<tr>
                                    <td>TRX" . str_pad($row['id_transaksi'], 3, '0', STR_PAD_LEFT) . "</td>
                                    <td>{$row['nama_petugas']}</td>
                                    <td>{$row['nama_sampah']}</td>
                                    <td>{$row['jumlah']}</td>
                                    <td>Rp" . number_format($row['total_harga'], 0, ',', '.') . "</td>
                                    <td>" . date('d-m-Y H:i:s', strtotime($row['tanggal'])) . "</td>
                                    <td><span class='badge bg-$badge'>" . ucfirst($row['status']) . "</span></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>Tidak ada riwayat transaksi.</td></tr>";
                        }

                        $stmt->close();
                        $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

$id_petugas = $_SESSION['user_id'];

$query = $conn->prepare("SELECT id_petugas FROM petugas WHERE id_petugas = ?");
$query->bind_param("s", $id_petugas);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();
$id_petugas = $data['id_petugas'] ?? 0;

if ($id_petugas == 0) {
    die("ID Petugas tidak ditemukan. Silakan login ulang.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = intval($_POST['id_user']);
    $id_sampah = intval($_POST['id_sampah']);
    $jumlah = $_POST['jumlah'];
    $tanggal = date('Y-m-d H:i:s');

    $sampahStmt = $conn->prepare("SELECT nama_sampah, harga FROM sampah WHERE id_sampah = ?");
    $sampahStmt->bind_param("i", $id_sampah);
    $sampahStmt->execute();
    $sampahData = $sampahStmt->get_result()->fetch_assoc();
    if (!$sampahData) {
        die("Data sampah tidak ditemukan.");
    }
    $nama_sampah = $sampahData['nama_sampah'];
    $harga = $sampahData['harga'];

    $status = 'menunggu';
    $stmt = $conn->prepare("INSERT INTO transaksi (id_petugas, id_user, id_sampah, nama_sampah, jumlah, harga, tanggal, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiississ", $id_petugas, $id_user, $id_sampah, $nama_sampah, $jumlah, $harga, $tanggal, $status);
    $stmt->execute();
    $stmt->close();
}

$users = $conn->query("SELECT id_user, nama_user FROM user ORDER BY nama_user");
$sampah = $conn->query("SELECT id_sampah, nama_sampah, harga FROM sampah");

$riwayat = $conn->prepare("
    SELECT t.*, u.nama_user 
    FROM transaksi t 
    JOIN user u ON t.id_user = u.id_user 
    WHERE t.id_petugas = ? 
    ORDER BY t.tanggal DESC
");
$riwayat->bind_param("i", $id_petugas);
$riwayat->execute();
$riwayat_result = $riwayat->get_result();
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Transaksi - Petugas Bank Sampah</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link href="../assets/css/petugas_transaksi.css" rel="stylesheet">
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
                    <a class="nav-link text-dark px-3 py-2" href="petugas_dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark px-3 py-2" href="petugas_penjemputan.php">
                        <i class="bi bi-truck me-2"></i> Penjemputan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="petugas_transaksi.php">
                        <i class="bi bi-cash-coin me-2"></i> Transaksi
                    </a>
                </li>
            </ul>
        </div>

        <main class="main-content">
            <h2 class="mb-4">Ajukan Transaksi ke User</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-4">
                            <label for="id_user" class="form-label">Pilih User</label>
                            <select class="form-select" id="id_user" name="id_user" required>
                                <option value="">-- Pilih --</option>
                                <?php while ($u = $users->fetch_assoc()): ?>
                                    <option value="<?= $u['id_user'] ?>"><?= htmlspecialchars($u['nama_user']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="id_sampah" class="form-label">Jenis Sampah</label>
                            <select class="form-select" id="id_sampah" name="id_sampah" required>
                                <option value="">-- Pilih --</option>
                                <?php while ($s = $sampah->fetch_assoc()): ?>
                                    <option value="<?= $s['id_sampah'] ?>">
                                         <?= htmlspecialchars($s['nama_sampah']) ?> (Rp<?= number_format($s['harga'], 2, ',', '.') ?>/kg)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                       <div class="col-md-4">
                            <label for="jumlah" class="form-label">Berat Sampah (kg)</label>
                            <input type="number" min="0.01" step="0.01" class="form-control" name="jumlah" required>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">Ajukan</button>
                        </div>
                    </form>
                </div>
            </div>

            <h4>Riwayat Transaksi</h4>
            <div class="table-responsive">
                <table class="table table-bordered mt-3">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Nama User</th>
                            <th>Jenis Sampah</th>
                            <th>Berat (Kg)</th>
                            <th>Harga</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($riwayat_result->num_rows > 0): 
                            while ($row = $riwayat_result->fetch_assoc()):
                                $badge = $row['status'] === 'diterima' ? 'success' : ($row['status'] === 'ditolak' ? 'danger' : 'secondary');
                        ?>
                            <tr>
                                <td>TRX<?= str_pad($row['id_transaksi'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($row['nama_user']) ?></td>
                                <td><?= htmlspecialchars($row['nama_sampah']) ?></td>
                                <td><?= number_format($row['jumlah'], 2, ',', '.') ?> kg</td>
                                <td>Rp<?= number_format($row['jumlah'] * $row['harga'], 2, ',', '.') ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
                                <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($row['status']) ?></span></td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center">Belum ada transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

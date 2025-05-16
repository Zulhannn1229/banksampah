<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../login.php");
    exit;
}

$id_petugas = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['aksi'])) {
    $id = intval($_POST['id']);
    $aksiInput = strtolower($_POST['aksi']);

    if (in_array($aksiInput, ['terima', 'tolak'])) {
        $statusBaru = $aksiInput === 'terima' ? 'Diterima' : 'Ditolak';

        $stmt = $conn->prepare("UPDATE penjemputan SET status = ?, id_petugas = ? WHERE id_penjemputan = ?");
        if ($stmt) {
            $stmt->bind_param("sii", $statusBaru, $id_petugas, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: petugas_penjemputan.php");
    exit;
}

$sql_pending = "
    SELECT p.id_penjemputan, p.tanggal, u.nama_user, u.alamat, u.no_hp
    FROM penjemputan p
    JOIN user u ON p.id_user = u.id_user
    WHERE p.status = 'Menunggu'
    ORDER BY p.tanggal DESC
";
$result_pending = $conn->query($sql_pending);

$stmt_history = $conn->prepare("
    SELECT p.tanggal, p.status, u.nama_user, u.alamat, u.no_hp
    FROM penjemputan p
    JOIN user u ON p.id_user = u.id_user
    WHERE p.status IN ('Diterima', 'Ditolak') AND p.id_petugas = ?
    ORDER BY p.tanggal DESC
");
$stmt_history->bind_param("i", $id_petugas);
$stmt_history->execute();
$result_history = $stmt_history->get_result();
?>


<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Penjemputan - Petugas Bank Sampah</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
        <link href="../assets/css/petugas_penjemputan.css" rel="stylesheet" />
        
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

        <!-- Sidebar -->
        <div class="custom-sidebar">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="petugas_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="petugas_user.php"><i class="bi bi-people me-2"></i> Data User</a></li>
                <li class="nav-item"><a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="petugas_penjemputan.php"><i class="bi bi-truck me-2"></i> Penjemputan</a></li>
                <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="petugas_transaksi.php"><i class="bi bi-cash-coin me-2"></i> Transaksi</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <main class="main-content">
            <h2 class="mb-4">Permintaan Penjemputan</h2>

            <?php if ($result_pending && $result_pending->num_rows > 0): ?>
                <?php while ($row = $result_pending->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            Permintaan dari <?= htmlspecialchars($row['nama_user']) ?>
                        </div>
                        <div class="card-body">
                            <p><strong>Alamat:</strong> <?= htmlspecialchars($row['alamat']) ?></p>
                            <p><strong>No. HP:</strong> <?= htmlspecialchars($row['no_hp']) ?></p>
                            <p><strong>Tanggal:</strong> <?= date('d-m-Y H:i:s', strtotime($row['tanggal'])) ?></p>
                            <form method="POST" class="d-flex gap-2 justify-content-end">
                                <input type="hidden" name="id" value="<?= $row['id_penjemputan'] ?>">
                                <button type="submit" name="aksi" value="terima" class="btn btn-success">Terima</button>
                                <button type="submit" name="aksi" value="tolak" class="btn btn-danger">Tolak</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">Tidak ada permintaan penjemputan yang menunggu.</div>
            <?php endif; ?>

            <hr class="my-5" />

            <h4>Riwayat Penjemputan</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-3">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Nama User</th>
                            <th>Alamat</th>
                            <th>No. HP</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_history && $result_history->num_rows > 0): 
                            $no = 1;
                            while ($row = $result_history->fetch_assoc()):
                                $badgeClass = match($row['status']) {
                                    'diterima' => 'success',
                                    'ditolak' => 'danger',
                                    default => 'secondary'
                                };
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama_user']) ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])) ?></td>
                                <td><span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($row['status']) ?></span></td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada riwayat penjemputan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </body>
</html>

<?php
require_once '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('user');

$id_user = (int) $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tanggal = date('Y-m-d H:i:s');
    $berat_perkiraan = isset($_POST['berat_perkiraan']) ? (float) $_POST['berat_perkiraan'] : 0;
    
    if ($berat_perkiraan <= 0) {
        $error = "Perkiraan berat sampah harus lebih dari 0 kg.";
    } else {
        // Cek apakah kolom berat_perkiraan sudah ada di database
        $checkColumn = $conn->query("SHOW COLUMNS FROM penjemputan LIKE 'berat_perkiraan'");
        
        if ($checkColumn->num_rows > 0) {
            // Kolom sudah ada, gunakan query dengan berat_perkiraan
            $stmt = $conn->prepare("INSERT INTO penjemputan (id_user, tanggal, berat_perkiraan) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isd", $id_user, $tanggal, $berat_perkiraan);
                if ($stmt->execute()) {
                    $success = "Pengajuan penjemputan berhasil dikirim!";
                } else {
                    $error = "Gagal mengajukan penjemputan: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Terjadi kesalahan saat mempersiapkan query.";
            }
        } else {
            // Kolom belum ada, gunakan query tanpa berat_perkiraan
            $stmt = $conn->prepare("INSERT INTO penjemputan (id_user, tanggal) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("is", $id_user, $tanggal);
                if ($stmt->execute()) {
                    $success = "Pengajuan penjemputan berhasil dikirim! (Catatan: Fitur berat sampah memerlukan update database)";
                } else {
                    $error = "Gagal mengajukan penjemputan: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error = "Terjadi kesalahan saat mempersiapkan query.";
            }
        }
    }
}

$riwayat = null;
$stmtRiwayat = $conn->prepare("SELECT * FROM penjemputan WHERE id_user = ? ORDER BY tanggal DESC");
if ($stmtRiwayat) {
    $stmtRiwayat->bind_param("i", $id_user);
    $stmtRiwayat->execute();
    $riwayat = $stmtRiwayat->get_result();
    $stmtRiwayat->close();
} else {
    $error = "Gagal mengambil riwayat penjemputan.";
}
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Penjemputan Sampah - User</title>
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
                    <a class="nav-link text-dark px-3 py-2" href="user_transaksi.php">
                        <i class="bi bi-cash-coin me-2"></i> Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="user_penjemputan.php">
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
            <h3 class="mb-4">Ajukan Penjemputan Sampah</h3>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" class="mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Pengajuan Penjemputan</h5>
                        <div class="mb-3">
                            <label for="berat_perkiraan" class="form-label">Perkiraan Berat Sampah (kg)</label>
                            <input type="number" class="form-control" id="berat_perkiraan" name="berat_perkiraan" 
                                   step="0.1" min="0.1" max="1000" required 
                                   placeholder="Masukkan perkiraan berat sampah dalam kg">
                            <div class="form-text">Masukkan perkiraan berat sampah yang akan dijemput (dalam kilogram)</div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-truck me-2"></i>Ajukan Penjemputan
                        </button>
                    </div>
                </div>
            </form>

            <h4>Riwayat Pengajuan Penjemputan</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-3">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Perkiraan Berat (kg)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($riwayat->num_rows > 0): 
                            $no = 1;
                            while ($row = $riwayat->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                    <td><?= number_format($row['berat_perkiraan'], 1); ?> kg</td>
                                    <td>
                                        <?php
                                        $status = $row['status'];
                                        $badgeClass = match ($status) {
                                            'diterima' => 'success',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badgeClass; ?>"><?= ucfirst($status); ?></span>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada pengajuan penjemputan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
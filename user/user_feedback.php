<?php
include '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('user');

$id_user = $_SESSION['user_id'] ?? null;
$success = '';
$error = '';

// Proses submit feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_feedback'])) {
    $judul = trim($_POST['judul']);
    $pesan = trim($_POST['pesan']);
    $kategori = $_POST['kategori'];

    if (empty($judul) || empty($pesan)) {
        $error = "Judul dan pesan tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (id_user, judul, pesan, kategori) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isss", $id_user, $judul, $pesan, $kategori);
            if ($stmt->execute()) {
                $success = "Feedback berhasil dikirim! Terima kasih atas masukan Anda.";
            } else {
                $error = "Gagal mengirim feedback: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Terjadi kesalahan saat mempersiapkan query.";
        }
    }
}

// Ambil riwayat feedback user
$riwayat = null;
$stmt_riwayat = $conn->prepare("SELECT * FROM feedback WHERE id_user = ? ORDER BY tanggal_kirim DESC");
if ($stmt_riwayat) {
    $stmt_riwayat->bind_param("i", $id_user);
    $stmt_riwayat->execute();
    $riwayat = $stmt_riwayat->get_result();
    $stmt_riwayat->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Feedback & Saran - User Bank Sampah</title>
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
                <a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="user_feedback.php">
                    <i class="bi bi-chat-square-text me-2"></i> Feedback & Saran
                </a>
            </li>
        </ul>
    </div>

    <main class="main-content">
      <h2 class="mb-4">Feedback & Saran</h2>

      <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php elseif ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Form Feedback -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="bi bi-envelope me-2"></i>Kirim Feedback atau Saran</h5>
        </div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="kirim_feedback" value="1">
            
            <div class="mb-3">
              <label for="kategori" class="form-label">Kategori</label>
              <select class="form-select" id="kategori" name="kategori" required>
                <option value="">Pilih Kategori</option>
                <option value="saran">Saran</option>
                <option value="keluhan">Keluhan</option>
                <option value="pujian">Pujian</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="judul" class="form-label">Judul</label>
              <input type="text" class="form-control" id="judul" name="judul" 
                     placeholder="Masukkan judul feedback atau saran" maxlength="200" required>
            </div>

            <div class="mb-3">
              <label for="pesan" class="form-label">Pesan</label>
              <textarea class="form-control" id="pesan" name="pesan" rows="5" 
                        placeholder="Tulis feedback, saran, atau keluhan Anda di sini..." required></textarea>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-success">
                <i class="bi bi-send me-2"></i>Kirim Feedback
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Riwayat Feedback -->
      <div class="card">
        <div class="card-header bg-secondary text-white">
          <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Feedback</h5>
        </div>
        <div class="card-body">
          <?php if ($riwayat && $riwayat->num_rows > 0): ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no = 1;
                  while ($row = $riwayat->fetch_assoc()): 
                    $badgeClass = match($row['status']) {
                      'baru' => 'primary',
                      'dibaca' => 'info',
                      'diproses' => 'warning',
                      'selesai' => 'success',
                      default => 'secondary'
                    };
                    
                    $categoryClass = match($row['kategori']) {
                      'saran' => 'success',
                      'keluhan' => 'danger',
                      'pujian' => 'info',
                      'lainnya' => 'secondary',
                      default => 'secondary'
                    };
                  ?>
                    <tr>
                      <td><?= $no++; ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($row['tanggal_kirim'])); ?></td>
                      <td>
                        <span class="badge bg-<?= $categoryClass; ?>">
                          <?= ucfirst($row['kategori']); ?>
                        </span>
                      </td>
                      <td><?= htmlspecialchars($row['judul']); ?></td>
                      <td>
                        <span class="badge bg-<?= $badgeClass; ?>">
                          <?= ucfirst($row['status']); ?>
                        </span>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalDetail<?= $row['id_feedback']; ?>">
                          <i class="bi bi-eye me-1"></i>Detail
                        </button>
                      </td>
                    </tr>

                    <!-- Modal Detail -->
                    <div class="modal fade" id="modalDetail<?= $row['id_feedback']; ?>" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Detail Feedback</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <div class="row mb-3">
                              <div class="col-md-6">
                                <strong>Kategori:</strong> 
                                <span class="badge bg-<?= $categoryClass; ?> ms-2">
                                  <?= ucfirst($row['kategori']); ?>
                                </span>
                              </div>
                              <div class="col-md-6">
                                <strong>Status:</strong> 
                                <span class="badge bg-<?= $badgeClass; ?> ms-2">
                                  <?= ucfirst($row['status']); ?>
                                </span>
                              </div>
                            </div>
                            <div class="mb-3">
                              <strong>Tanggal Kirim:</strong> 
                              <?= date('d F Y, H:i', strtotime($row['tanggal_kirim'])); ?> WIB
                            </div>
                            <?php if ($row['tanggal_baca']): ?>
                            <div class="mb-3">
                              <strong>Tanggal Dibaca:</strong> 
                              <?= date('d F Y, H:i', strtotime($row['tanggal_baca'])); ?> WIB
                            </div>
                            <?php endif; ?>
                            <div class="mb-3">
                              <strong>Judul:</strong><br>
                              <?= htmlspecialchars($row['judul']); ?>
                            </div>
                            <div class="mb-3">
                              <strong>Pesan:</strong><br>
                              <div class="p-3 bg-light rounded">
                                <?= nl2br(htmlspecialchars($row['pesan'])); ?>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="text-center py-4">
              <i class="bi bi-inbox display-1 text-muted"></i>
              <p class="text-muted mt-3">Belum ada feedback yang dikirim.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
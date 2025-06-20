<?php
include '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('admin');

$success = '';
$error = '';

// Update status feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_feedback = intval($_POST['id_feedback']);
    $status_baru = $_POST['status'];
    
    // Update status dan tanggal baca jika status dibaca
    if ($status_baru === 'dibaca') {
        $stmt = $conn->prepare("UPDATE feedback SET status = ?, tanggal_baca = NOW() WHERE id_feedback = ?");
    } else {
        $stmt = $conn->prepare("UPDATE feedback SET status = ? WHERE id_feedback = ?");
    }
    
    if ($stmt) {
        $stmt->bind_param("si", $status_baru, $id_feedback);
        if ($stmt->execute()) {
            $success = "Status feedback berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui status feedback.";
        }
        $stmt->close();
    }
}

// Hapus feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_feedback'])) {
    $id_feedback = intval($_POST['id_feedback']);
    
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id_feedback = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_feedback);
        if ($stmt->execute()) {
            $success = "Feedback berhasil dihapus.";
        } else {
            $error = "Gagal menghapus feedback.";
        }
        $stmt->close();
    }
}

// Ambil statistik feedback
$stats = [];
$result_stats = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'baru' THEN 1 ELSE 0 END) as baru,
        SUM(CASE WHEN status = 'dibaca' THEN 1 ELSE 0 END) as dibaca,
        SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
        SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai
    FROM feedback
");
if ($result_stats) {
    $stats = $result_stats->fetch_assoc();
}

// Filter feedback
$filter_status = $_GET['status'] ?? '';
$filter_kategori = $_GET['kategori'] ?? '';

$where_clause = [];
$params = [];
$types = '';

if ($filter_status) {
    $where_clause[] = "f.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if ($filter_kategori) {
    $where_clause[] = "f.kategori = ?";
    $params[] = $filter_kategori;
    $types .= 's';
}

$where_sql = $where_clause ? 'WHERE ' . implode(' AND ', $where_clause) : '';

// Ambil semua feedback dengan informasi user
$sql = "
    SELECT f.*, u.nama_user, u.email 
    FROM feedback f 
    JOIN user u ON f.id_user = u.id_user 
    $where_sql
    ORDER BY f.tanggal_kirim DESC
";

$stmt = $conn->prepare($sql);
if ($stmt) {
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $feedback_list = $stmt->get_result();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Feedback - Admin Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../assets/css/admin.css" rel="stylesheet" />
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
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_user.php"><i class="bi bi-people me-2"></i> Data User</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_petugas.php"><i class="bi bi-person-badge me-2"></i> Data Petugas</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_sampah.php"><i class="bi bi-trash me-2"></i> Data Sampah</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_berita.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a></li>
        <a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="admin_feedback.php"><i class="bi bi-chat-square-text me-2"></i> Feedback</a></li>
    </ul>
</div>

    <main class="main-content">
      <h2 class="mb-4">Kelola Feedback & Saran</h2>

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

      <!-- Statistik Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card text-white bg-primary">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title">Total Feedback</h5>
                  <h3><?= $stats['total'] ?? 0; ?></h3>
                </div>
                <i class="bi bi-chat-square-text display-4"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-warning">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title">Feedback Baru</h5>
                  <h3><?= $stats['baru'] ?? 0; ?></h3>
                </div>
                <i class="bi bi-exclamation-circle display-4"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-info">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title">Sedang Diproses</h5>
                  <h3><?= $stats['diproses'] ?? 0; ?></h3>
                </div>
                <i class="bi bi-gear display-5"></i>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card text-white bg-success">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="card-title">Selesai</h5>
                  <h3><?= $stats['selesai'] ?? 0; ?></h3>
                </div>
                <i class="bi bi-check-circle display-4"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="card mb-4">
        <div class="card-body">
          <form method="GET" class="row g-3">
            <div class="col-md-4">
              <label for="status" class="form-label">Filter Status</label>
              <select class="form-select" id="status" name="status">
                <option value="">Semua Status</option>
                <option value="baru" <?= $filter_status === 'baru' ? 'selected' : ''; ?>>Baru</option>
                <option value="dibaca" <?= $filter_status === 'dibaca' ? 'selected' : ''; ?>>Dibaca</option>
                <option value="diproses" <?= $filter_status === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                <option value="selesai" <?= $filter_status === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="kategori" class="form-label">Filter Kategori</label>
              <select class="form-select" id="kategori" name="kategori">
                <option value="">Semua Kategori</option>
                <option value="saran" <?= $filter_kategori === 'saran' ? 'selected' : ''; ?>>Saran</option>
                <option value="keluhan" <?= $filter_kategori === 'keluhan' ? 'selected' : ''; ?>>Keluhan</option>
                <option value="pujian" <?= $filter_kategori === 'pujian' ? 'selected' : ''; ?>>Pujian</option>
                <option value="lainnya" <?= $filter_kategori === 'lainnya' ? 'selected' : ''; ?>>Lainnya</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">&nbsp;</label>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="admin_feedback.php" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Daftar Feedback -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Daftar Feedback</h5>
        </div>
        <div class="card-body">
          <?php if ($feedback_list && $feedback_list->num_rows > 0): ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Pengirim</th>
                    <th>Kategori</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no = 1;
                  while ($row = $feedback_list->fetch_assoc()): 
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
                    <tr class="<?= $row['status'] === 'baru' ? 'table-warning' : ''; ?>">
                      <td><?= $no++; ?></td>
                      <td>
                        <strong><?= htmlspecialchars($row['nama_user']); ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($row['email']); ?></small>
                      </td>
                      <td>
                        <span class="badge bg-<?= $categoryClass; ?>">
                          <?= ucfirst($row['kategori']); ?>
                        </span>
                      </td>
                      <td><?= htmlspecialchars($row['judul']); ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($row['tanggal_kirim'])); ?></td>
                      <td>
                        <span class="badge bg-<?= $badgeClass; ?>">
                          <?= ucfirst($row['status']); ?>
                        </span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <button class="btn btn-sm btn-outline-primary" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#modalDetail<?= $row['id_feedback']; ?>">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-success" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#modalStatus<?= $row['id_feedback']; ?>">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger" 
                                  data-bs-toggle="modal" 
                                  data-bs-target="#modalHapus<?= $row['id_feedback']; ?>">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
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
                                <strong>Pengirim:</strong><br>
                                <?= htmlspecialchars($row['nama_user']); ?><br>
                                <small class="text-muted"><?= htmlspecialchars($row['email']); ?></small>
                              </div>
                              <div class="col-md-6">
                                <strong>Kategori:</strong> 
                                <span class="badge bg-<?= $categoryClass; ?>">
                                  <?= ucfirst($row['kategori']); ?>
                                </span><br>
                                <strong>Status:</strong> 
                                <span class="badge bg-<?= $badgeClass; ?>">
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
                            <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalStatus<?= $row['id_feedback']; ?>"
                                    data-bs-dismiss="modal">
                              <i class="bi bi-pencil me-1"></i>Ubah Status
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Modal Update Status -->
                    <div class="modal fade" id="modalStatus<?= $row['id_feedback']; ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Update Status Feedback</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <form method="POST">
                            <div class="modal-body">
                              <input type="hidden" name="update_status" value="1">
                              <input type="hidden" name="id_feedback" value="<?= $row['id_feedback']; ?>">
                              
                              <div class="mb-3">
                                <strong>Judul:</strong> <?= htmlspecialchars($row['judul']); ?>
                              </div>
                              <div class="mb-3">
                                <strong>Pengirim:</strong> <?= htmlspecialchars($row['nama_user']); ?>
                              </div>
                              <div class="mb-3">
                                <label for="status<?= $row['id_feedback']; ?>" class="form-label">Status Baru</label>
                                <select class="form-select" id="status<?= $row['id_feedback']; ?>" name="status" required>
                                  <option value="baru" <?= $row['status'] === 'baru' ? 'selected' : ''; ?>>Baru</option>
                                  <option value="dibaca" <?= $row['status'] === 'dibaca' ? 'selected' : ''; ?>>Dibaca</option>
                                  <option value="diproses" <?= $row['status'] === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                  <option value="selesai" <?= $row['status'] === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-success">
                                <i class="bi bi-check me-1"></i>Update Status
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    <!-- Modal Hapus -->
                    <div class="modal fade" id="modalHapus<?= $row['id_feedback']; ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <p>Apakah Anda yakin ingin menghapus feedback ini?</p>
                            <div class="alert alert-warning">
                              <strong>Judul:</strong> <?= htmlspecialchars($row['judul']); ?><br>
                              <strong>Pengirim:</strong> <?= htmlspecialchars($row['nama_user']); ?>
                            </div>
                            <p class="text-danger"><strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan!</p>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <form method="POST" class="d-inline">
                              <input type="hidden" name="hapus_feedback" value="1">
                              <input type="hidden" name="id_feedback" value="<?= $row['id_feedback']; ?>">
                              <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Hapus
                              </button>
                            </form>
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
              <p class="text-muted mt-3">Tidak ada feedback yang ditemukan.</p>
              <?php if ($filter_status || $filter_kategori): ?>
                <a href="admin_feedback.php" class="btn btn-outline-primary">
                  <i class="bi bi-arrow-clockwise me-1"></i>Lihat Semua Feedback
                </a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
      // Auto refresh page every 5 minutes untuk update status baru
      setInterval(function() {
        if (<?= ($stats['baru'] ?? 0) > 0 ? 'true' : 'false' ?>) {
          // Only refresh if there are new feedbacks
          location.reload();
        }
      }, 300000); // 5 menit

      // Highlight feedback baru
      document.addEventListener('DOMContentLoaded', function() {
        const newFeedbackRows = document.querySelectorAll('.table-warning');
        newFeedbackRows.forEach(function(row) {
          row.style.animation = 'pulse 2s infinite';
        });
      });

      // Konfirmasi sebelum hapus
      document.querySelectorAll('form').forEach(function(form) {
        if (form.querySelector('input[name="hapus_feedback"]')) {
          form.addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus feedback ini?')) {
              e.preventDefault();
            }
          });
        }
      });
    </script>

    <style>
      @keyframes pulse {
        0% { background-color: #fff3cd; }
        50% { background-color: #ffeaa7; }
        100% { background-color: #fff3cd; }
      }
      
      .table-warning {
        animation: pulse 2s ease-in-out infinite;
      }
      
      .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
      }
      
      .badge {
        font-size: 0.75em;
      }
      
      .btn-group .btn {
        margin-right: 2px;
      }
      
      .modal-body .alert {
        margin-bottom: 1rem;
      }
    </style>
  </body>
</html>
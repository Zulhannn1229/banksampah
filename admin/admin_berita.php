<?php
include '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $isi   = $_POST['isi'];
    $tanggal = date('Y-m-d');
    $gambarName = null;

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambarName = time() . '-' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/" . $gambarName);
    }

    if (isset($_POST['edit_id'])) {
        $id = (int)$_POST['edit_id'];
        if ($gambarName) {
            $stmt = $conn->prepare("UPDATE berita SET judul = ?, isi = ?, gambar = ? WHERE id_berita = ?");
            $stmt->bind_param("sssi", $judul, $isi, $gambarName, $id);
        } else {
            $stmt = $conn->prepare("UPDATE berita SET judul = ?, isi = ? WHERE id_berita = ?");
            $stmt->bind_param("ssi", $judul, $isi, $id);
        }
        $stmt->execute();
    } else {
        // Tambah
        $stmt = $conn->prepare("INSERT INTO berita (judul, isi, gambar, tanggal) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $judul, $isi, $gambarName, $tanggal);
        $stmt->execute();
    }

    header("Location: admin_berita.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM berita WHERE id_berita = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin_berita.php");
    exit;
}

$berita = $conn->query("SELECT * FROM berita ORDER BY id_berita DESC");
$editBerita = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM berita WHERE id_berita = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $editBerita = $result->fetch_assoc();
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Kelola Berita - Admin Bank Sampah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="../assets/css/admin.css" rel="stylesheet" rel="stylesheet" />
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

<div class="custom-sidebar d-none d-md-block">
  <ul class="nav flex-column">
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_user.php"><i class="bi bi-people me-2"></i> Data User</a></li>
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_petugas.php"><i class="bi bi-person-badge me-2"></i> Data Petugas</a></li>
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_sampah.php"><i class="bi bi-trash me-2"></i> Data Sampah</a></li>
    <li class="nav-item"><a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="admin_berita.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a></li>
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_feedback.php"><i class="bi bi-chat-square-text me-2"></i> Feedback</a></li>
  </ul>
</div>

<main class="main-content">
  <div class="container-fluid">
    <h2 class="mb-4"><?= $editBerita ? 'Edit Berita' : 'Tambah Berita' ?></h2>
    <div class="card mb-4">
      <div class="card-header bg-success text-white"><?= $editBerita ? 'Form Edit' : 'Form Tambah' ?></div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <?php if ($editBerita): ?>
            <input type="hidden" name="edit_id" value="<?= $editBerita['id_berita'] ?>">
          <?php endif; ?>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Judul Berita</label>
              <input type="text" class="form-control" name="judul" required value="<?= $editBerita['judul'] ?? '' ?>">
            </div>
            <div class="col-12">
              <label class="form-label">Isi Berita</label>
              <textarea class="form-control" name="isi" rows="4" required><?= $editBerita['isi'] ?? '' ?></textarea>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Gambar (opsional)</label>
              <input type="file" class="form-control" name="gambar">
            </div>
          </div>
          <div class="mt-3">
            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan</button>
            <?php if ($editBerita): ?>
              <a href="admin_berita.php" class="btn btn-secondary ms-2">Batal</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-success text-center">
          <tr>
            <th>No</th>
            <th>Gambar</th>
            <th>Judul</th>
            <th>Isi</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php $no = 1; while($row = $berita->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td>
                <?php if ($row['gambar']): ?>
                  <img src="../uploads/<?= $row['gambar'] ?>" class="img-thumbnail" width="60">
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['judul']) ?></td>
              <td><?= substr(strip_tags($row['isi']), 0, 50) ?>...</td>
              <td><?= $row['tanggal'] ?></td>
              <td>
                <a href="admin_berita.php?edit=<?= $row['id_berita'] ?>" class="btn btn-sm btn-primary w-100 mb-1"><i class="bi bi-pencil-square"></i> Edit</a>
                <a href="admin_berita.php?hapus=<?= $row['id_berita'] ?>" class="btn btn-sm btn-danger w-100" onclick="return confirm('Hapus berita ini?')"><i class="bi bi-trash"></i> Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

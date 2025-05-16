<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'] ?? null;
$username = $_SESSION['username'] ?? 'User';

$stmt = $conn->prepare("SELECT nama_user, email, no_hp, alamat FROM user WHERE id_user=?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($nama, $email, $telepon, $alamat);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nama_baru    = $_POST['nama'];
    $email_baru   = $_POST['email'];
    $telepon_baru = $_POST['telepon'];
    $alamat_baru  = $_POST['alamat'];

    $stmt = $conn->prepare("UPDATE user SET nama_user=?, email=?, no_hp=?, alamat=? WHERE id_user=?");
    $stmt->bind_param("ssssi", $nama_baru, $email_baru, $telepon_baru, $alamat_baru, $id_user);

    if ($stmt->execute()) {
        $success = "Profil berhasil diperbarui.";
        $nama = $nama_baru;
        $email = $email_baru;
        $telepon = $telepon_baru;
        $alamat = $alamat_baru;
    } else {
        $error = "Gagal memperbarui profil.";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM user WHERE id_user=?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $stmt->bind_result($hash);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($old, $hash)) {
            $error = "Password lama salah.";
        } else {
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user SET password=? WHERE id_user=?");
            $stmt->bind_param("si", $new_hash, $id_user);
            if ($stmt->execute()) {
                $success = "Password berhasil diubah.";
            } else {
                $error = "Gagal mengubah password.";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profil User - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="../assets/css/user_profile.css" rel="stylesheet" />
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
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="user_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="user_transaksi.php"><i class="bi bi-cash-coin me-2"></i> Transaksi</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="user_penjemputan.php"><i class="bi bi-truck me-2"></i> Penjemputan</a></li>
        <li class="nav-item"><a class="nav-link active text-dark px-3 py-2 bg-success bg-opacity-25 rounded" href="#"><i class="bi bi-person-circle me-2"></i> Profil</a></li>
      </ul>
    </div>

    <main class="main-content">
      <h2 class="mb-4">Profil Saya</h2>

      <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body">
          <p><strong>Nama:</strong> <?= htmlspecialchars($nama) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
          <p><strong>No. Telepon:</strong> <?= htmlspecialchars($telepon) ?></p>
          <p><strong>Alamat:</strong> <?= htmlspecialchars($alamat) ?></p>
          <div class="mt-4">
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#modalUbahProfil">
              <i class="bi bi-pencil-square me-1"></i> Ubah Profil
            </button>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalUbahPassword">
              <i class="bi bi-shield-lock me-1"></i> Ubah Kata Sandi
            </button>
          </div>
        </div>
      </div>
    </main>

    <div class="modal fade" id="modalUbahProfil" tabindex="-1" aria-labelledby="modalUbahProfilLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form class="modal-content" method="POST">
          <input type="hidden" name="update_profil" value="1">
          <div class="modal-header">
            <h5 class="modal-title">Ubah Profil</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="nama" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-3">
              <label for="telepon" class="form-label">No. Telepon</label>
              <input type="text" class="form-control" id="telepon" name="telepon" value="<?= htmlspecialchars($telepon) ?>" required>
            </div>
            <div class="mb-3">
              <label for="alamat" class="form-label">Alamat</label>
              <textarea class="form-control" id="alamat" name="alamat" required><?= htmlspecialchars($alamat) ?></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Simpan</button>
          </div>
        </form>
      </div>
    </div>
    <div class="modal fade" id="modalUbahPassword" tabindex="-1" aria-labelledby="modalUbahPasswordLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form class="modal-content" method="POST">
          <input type="hidden" name="update_password" value="1">
          <div class="modal-header">
            <h5 class="modal-title">Ubah Kata Sandi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="oldPassword" class="form-label">Kata Sandi Lama</label>
              <input type="password" class="form-control" id="oldPassword" name="old_password" required>
            </div>
            <div class="mb-3">
              <label for="newPassword" class="form-label">Kata Sandi Baru</label>
              <input type="password" class="form-control" id="newPassword" name="new_password" required>
            </div>
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi Baru</label>
              <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Simpan</button>
          </div>
        </form>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

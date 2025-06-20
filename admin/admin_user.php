<?php
require_once '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('admin');

function sanitize($data) {
    return htmlspecialchars(trim($data));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhoneNumber($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama     = sanitize($_POST['nama_user']);
    $username = sanitize($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email    = sanitize($_POST['email']);
    $nohp     = sanitize($_POST['no_hp']);
    $alamat   = sanitize($_POST['alamat']);

    if (!validateEmail($email)) {
        $_SESSION['error'] = "Email tidak valid.";
    } elseif (!validatePhoneNumber($nohp)) {
        $_SESSION['error'] = "Nomor HP tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "Username sudah terdaftar.";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO user (nama_user, username, password, email, no_hp, alamat, role) 
                                    VALUES (?, ?, ?, ?, ?, ?, 'user')");
            $stmt->bind_param("ssssss", $nama, $username, $password, $email, $nohp, $alamat);
            if ($stmt->execute()) {
                $_SESSION['success'] = "User berhasil ditambahkan.";
            } else {
                $_SESSION['error'] = "Gagal menambahkan user.";
            }
        }
        $stmt->close();
    }
    header("Location: admin_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id       = intval($_POST['id']);
    $nama     = sanitize($_POST['nama_user']);
    $username = sanitize($_POST['username']);
    $email    = sanitize($_POST['email']);
    $nohp     = sanitize($_POST['no_hp']);
    $alamat   = sanitize($_POST['alamat']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!validateEmail($email)) {
        $_SESSION['error'] = "Email tidak valid.";
    } elseif (!validatePhoneNumber($nohp)) {
        $_SESSION['error'] = "Nomor HP tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT id_user FROM user WHERE username = ? AND id_user != ?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = "Username sudah digunakan oleh user lain.";
        } else {
            $stmt->close();

            if (!empty($password)) {
                // Hash password jika diisi
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE user SET nama_user=?, username=?, email=?, no_hp=?, alamat=?, password=? WHERE id_user=?");
                $stmt->bind_param("ssssssi", $nama, $username, $email, $nohp, $alamat, $hashed_password, $id);
            } else {
                // Jika password tidak diubah
                $stmt = $conn->prepare("UPDATE user SET nama_user=?, username=?, email=?, no_hp=?, alamat=? WHERE id_user=?");
                $stmt->bind_param("sssssi", $nama, $username, $email, $nohp, $alamat, $id);
            }

            if ($stmt->execute()) {
                $_SESSION['success'] = "User berhasil diperbarui.";
            } else {
                $_SESSION['error'] = "Gagal memperbarui user.";
            }
        }
        $stmt->close();
    }

    header("Location: admin_user.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    $stmt = $conn->prepare("DELETE FROM user WHERE id_user=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "User berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus user.";
    }
    $stmt->close();

    header("Location: admin_user.php");
    exit;
}

$data = $conn->query("SELECT * FROM user");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data User - Admin Bank Sampah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/admin.css" rel="stylesheet">
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
    <li class="nav-item"><a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="admin_user.php"><i class="bi bi-people me-2"></i> Data User</a></li>
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_petugas.php"><i class="bi bi-person-badge me-2"></i> Data Petugas</a></li>
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_sampah.php"><i class="bi bi-trash me-2"></i> Data Sampah</a></li>
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_berita.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a></li>
    <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_feedback.php"><i class="bi bi-chat-square-text me-2"></i> Feedback</a></li>
  </ul>
</div>

<main class="main-content">
  <h1 class="mb-4">Data User</h1>

<?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $_SESSION['error']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="card mb-4">
  <div class="card-header bg-success text-white">Tambah User</div>
  <div class="card-body">
    <form method="post">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" name="nama_user" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Username</label>
          <input type="text" class="form-control" name="username" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="password" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">No HP</label>
          <input type="text" class="form-control" name="no_hp" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Alamat</label>
          <textarea class="form-control" name="alamat" rows="2" required></textarea>
        </div>
      </div>
      <div class="mt-3">
        <button type="submit" name="tambah" class="btn btn-success">
          <i class="bi bi-save me-1"></i> Simpan User
        </button>
      </div>
    </form>
  </div>
</div>

<div class="table-responsive">
  <table class="table table-bordered table-striped align-middle">
    <thead class="table-success text-center">
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Username</th>
        <th>Email</th>
        <th>No HP</th>
        <th>Alamat</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody class="text-center">
      <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['nama_user']); ?></td>
          <td><?= htmlspecialchars($row['username']); ?></td>
          <td><?= htmlspecialchars($row['email']); ?></td>
          <td><?= htmlspecialchars($row['no_hp']); ?></td>
          <td><?= htmlspecialchars($row['alamat']); ?></td>
          <td>
            <div class="d-flex justify-content-center gap-2">
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_user']; ?>">
                <i class="bi bi-pencil-square"></i> Edit
              </button>
              <a href="?hapus=<?= $row['id_user']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">
                <i class="bi bi-trash"></i> Hapus
              </a>
            </div>
          </td>
        </tr>

        <div class='modal fade' id='editModal<?= $row['id_user']; ?>' tabindex='-1'>
          <div class='modal-dialog modal-dialog-centered modal-sm'>
            <div class='modal-content'>
              <form method='post'>
                <input type='hidden' name='id' value='<?= $row['id_user']; ?>'>
                <div class='modal-header bg-success text-white py-2'>
                  <h6 class='modal-title'>Edit User</h6>
                  <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                </div>
                <div class='modal-body p-2'>
                  <div class='mb-2'>
                    <input type='text' class='form-control form-control-sm' name='nama_user' placeholder='Nama' value='<?= htmlspecialchars($row['nama_user']); ?>' required>
                  </div>
                  <div class='mb-2'>
                    <input type='text' class='form-control form-control-sm' name='username' placeholder='Username' value='<?= htmlspecialchars($row['username']); ?>' required>
                  </div>
                  <div class='mb-2'>
                    <input type='email' class='form-control form-control-sm' name='email' placeholder='Email' value='<?= htmlspecialchars($row['email']); ?>' required>
                  </div>
                  <div class='mb-2'>
                    <input type='text' class='form-control form-control-sm' name='no_hp' placeholder='No HP' value='<?= htmlspecialchars($row['no_hp']); ?>' required>
                  </div>
                  <div class='mb-2'>
                    <textarea class='form-control form-control-sm' name='alamat' placeholder='Alamat' rows='2' required><?= htmlspecialchars($row['alamat']); ?></textarea>
                  </div>
                  <div class='mb-2'>
                    <input type='password' class='form-control form-control-sm' name='password' placeholder='Password (opsional)'>
                  </div>
                </div>
                <div class='modal-footer p-2'>
                  <button type='submit' name='edit' class='btn btn-sm btn-primary w-100'>Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>


</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

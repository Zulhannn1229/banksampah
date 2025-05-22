<?php
require_once '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('admin');

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $nama     = sanitize($_POST['nama_petugas']);
    $username = sanitize($_POST['username']);
    $no_hp    = sanitize($_POST['no_hp']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id_petugas FROM petugas WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Username sudah terdaftar.";
    } else {
        $query = $conn->prepare("INSERT INTO petugas (nama_petugas, username, password, no_hp, role) VALUES (?, ?, ?, ?, 'petugas')");
        $query->bind_param("ssss", $nama, $username, $password, $no_hp);
        if ($query->execute()) {
            $_SESSION['success'] = "Petugas berhasil ditambahkan.";
        } else {
            $_SESSION['error'] = "Gagal menambahkan petugas.";
        }
        $query->close();
    }
    $stmt->close();
    header("Location: admin_petugas.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id       = intval($_POST['id']);
    $nama     = sanitize($_POST['nama_petugas']);
    $username = sanitize($_POST['username']);
    $no_hp    = sanitize($_POST['no_hp']);

    $cek = $conn->prepare("SELECT id_petugas FROM petugas WHERE username = ? AND id_petugas != ?");
    $cek->bind_param("si", $username, $id);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $_SESSION['error'] = "Username sudah digunakan oleh petugas lain.";
    } else {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = $conn->prepare("UPDATE petugas SET nama_petugas=?, username=?, password=?, no_hp=? WHERE id_petugas=?");
            $query->bind_param("ssssi", $nama, $username, $password, $no_hp, $id);
        } else {
            $query = $conn->prepare("UPDATE petugas SET nama_petugas=?, username=?, no_hp=? WHERE id_petugas=?");
            $query->bind_param("sssi", $nama, $username, $no_hp, $id);
        }

        if ($query->execute()) {
            $_SESSION['success'] = "Petugas berhasil diperbarui.";
        } else {
            $_SESSION['error'] = "Gagal memperbarui data petugas.";
        }
        $query->close();
    }
    $cek->close();
    header("Location: admin_petugas.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    $cek = $conn->prepare("SELECT id_petugas FROM petugas WHERE id_petugas = ?");
    $cek->bind_param("i", $id);
    $cek->execute();
    $cek_result = $cek->get_result();

    if ($cek_result->num_rows > 0) {
        $hapus = $conn->prepare("DELETE FROM petugas WHERE id_petugas = ?");
        $hapus->bind_param("i", $id);
        if ($hapus->execute()) {
            $_SESSION['success'] = "Petugas berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus petugas.";
        }
        $hapus->close();
    } else {
        $_SESSION['error'] = "Petugas tidak ditemukan.";
    }
    $cek->close();
    header("Location: admin_petugas.php");
    exit;
}

$result = $conn->query("SELECT * FROM petugas");
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Petugas - Admin Bank Sampah</title>
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
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_user.php"><i class="bi bi-people me-2"></i> Data User</a></li>
        <li class="nav-item"><a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="admin_petugas.php"><i class="bi bi-person-badge me-2"></i> Data Petugas</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_sampah.php"><i class="bi bi-trash me-2"></i> Data Sampah</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_berita.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a></li>
    </ul>
</div>

<main class="main-content">
    <h2 class="mb-4">Tambah Petugas</h2>
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
        <div class="card-header bg-success text-white">Form Tambah Petugas</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_petugas" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" name="tambah" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> Tambah</button>
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
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['nama_petugas']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['no_hp']}</td>
                        <td>
                            <button class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#editModal{$row['id_petugas']}'><i class='bi bi-pencil-square'></i> Edit</button>
                            <a href='admin_petugas.php?hapus={$row['id_petugas']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus data ini?')\"><i class='bi bi-trash'></i> Hapus</a>
                        </td>
                    </tr>";

                    echo "
                    <div class='modal fade' id='editModal{$row['id_petugas']}' tabindex='-1'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <form method='post'>
                                    <input type='hidden' name='id' value='{$row['id_petugas']}'>
                                    <div class='modal-header bg-success text-white'>
                                        <h5 class='modal-title'>Edit Petugas</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                    </div>
                                    <div class='modal-body'>
                                        <div class='mb-2'>
                                            <label class='form-label'>Nama Lengkap</label>
                                            <input type='text' class='form-control' name='nama_petugas' value='" . htmlspecialchars($row['nama_petugas']) . "' required>
                                        </div>
                                        <div class='mb-2'>
                                            <label class='form-label'>Username</label>
                                            <input type='text' class='form-control' name='username' value='" . htmlspecialchars($row['username']) . "' required>
                                        </div>
                                        <div class='mb-2'>
                                            <label class='form-label'>No HP</label>
                                            <input type='text' class='form-control' name='no_hp' value='" . htmlspecialchars($row['no_hp']) . "' required>
                                        </div>
                                        <div class='mb-2'>
                                            <label class='form-label'>Password (Kosongkan jika tidak ingin diubah)</label>
                                            <input type='password' class='form-control' name='password'>
                                        </div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='submit' name='edit' class='btn btn-primary'>Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
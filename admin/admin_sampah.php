<?php
require_once '../config/db.php';
require_once '../auth/checkAuth.php';
checkAuth('admin');

$isEdit = isset($_GET['edit']);
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['jenis'];
    $harga = $_POST['harga'];

    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $gambarName = uniqid() . '_' . basename($_FILES['gambar']['name']);
        $uploadPath = $uploadDir . $gambarName;

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($gambarName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            die("Hanya file gambar yang diperbolehkan.");
        }

        if ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
            die("Ukuran file terlalu besar, maksimal 5MB.");
        }

        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath)) {
            die("Gagal mengupload gambar.");
        }

        $gambar = $gambarName;
    }

    if ($isEdit) {
        $id = $_GET['edit'];
        if ($gambar) {
            $query = $conn->prepare("UPDATE sampah SET nama_sampah=?, harga=?, gambar=? WHERE id_sampah=?");
            $query->bind_param("sssi", $nama, $harga, $gambar, $id);
        } else {
            $query = $conn->prepare("UPDATE sampah SET nama_sampah=?, harga=? WHERE id_sampah=?");
            $query->bind_param("ssi", $nama, $harga, $id);
        }
    } else {
        $query = $conn->prepare("INSERT INTO sampah (nama_sampah, harga, gambar) VALUES (?, ?, ?)");
        $query->bind_param("sis", $nama, $harga, $gambar);
    }

    $query->execute();
    header("Location: admin_sampah.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $res = $conn->prepare("SELECT gambar FROM sampah WHERE id_sampah = ?");
    $res->bind_param("i", $id);
    $res->execute();
    $result = $res->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['gambar']) {
        $gambarPath = '../uploads/' . $row['gambar'];
        if (file_exists($gambarPath)) {
            unlink($gambarPath);
        }
    }

    $deleteQuery = $conn->prepare("DELETE FROM sampah WHERE id_sampah = ?");
    $deleteQuery->bind_param("i", $id);
    $deleteQuery->execute();
    header("Location: admin_sampah.php");
    exit;
}

if ($isEdit) {
    $id = $_GET['edit'];
    $query = $conn->prepare("SELECT * FROM sampah WHERE id_sampah = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $editData = $result->fetch_assoc();
}

$dataSampah = $conn->query("SELECT * FROM sampah");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Sampah - Admin Bank Sampah</title>
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

<div class="custom-sidebar d-none d-md-block">
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_user.php"><i class="bi bi-people me-2"></i> Data User</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_petugas.php"><i class="bi bi-person-badge me-2"></i> Data Petugas</a></li>
        <li class="nav-item"><a class="nav-link active text-dark bg-success bg-opacity-25 rounded px-3 py-2" href="admin_sampah.php"><i class="bi bi-trash me-2"></i> Data Sampah</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_berita.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a></li>
        <li class="nav-item"><a class="nav-link text-dark px-3 py-2" href="admin_feedback.php"><i class="bi bi-chat-square-text me-2"></i> Feedback</a></li>
    </ul>
</div>

<main class="main-content">
    <h2 class="mb-4">Data Sampah</h2>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <?= $isEdit ? 'Edit Data Sampah' : 'Tambah Data Sampah'; ?>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="jenis" class="form-label">Jenis/Nama Sampah</label>
                        <input type="text" class="form-control" id="jenis" name="jenis"
                            value="<?= $editData['nama_sampah'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="harga" class="form-label">Harga per Kg (Rp)</label>
                        <input type="number" class="form-control" id="harga" name="harga"
                            value="<?= $editData['harga'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="gambar" class="form-label">Gambar <?= $isEdit ? '(Kosongkan jika tidak diubah)' : '' ?></label>
                        <input type="file" class="form-control" id="gambar" name="gambar">
                    </div>
                </div>
                <div class="mt-3">
                    <?php if ($isEdit): ?>
                        <a href="admin_sampah.php" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Simpan Data Sampah
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
                    <th>Gambar</th>
                    <th>Jenis Sampah</th>
                    <th>Harga/Kg</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($dataSampah)) {
                    $gambarUrl = $row['gambar'] ? "../uploads/{$row['gambar']}" : "https://via.placeholder.com/60";
                    echo "<tr>
                            <td>{$no}</td>
                            <td><img src='{$gambarUrl}' class='img-thumbnail' width='60'></td>
                            <td>{$row['nama_sampah']}</td>
                            <td>Rp " . number_format($row['harga'], 2, ',', '.') . "</td>
                            <td>
                                <a href='admin_sampah.php?edit={$row['id_sampah']}' class='btn btn-sm btn-primary'>
                                    <i class='bi bi-pencil-square'></i> Edit
                                </a>
                                <a href='admin_sampah.php?hapus={$row['id_sampah']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">
                                    <i class='bi bi-trash'></i> Hapus
                                </a>
                            </td>
                        </tr>";
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
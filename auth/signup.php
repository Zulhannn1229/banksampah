<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config/db.php';

    $fullname = isset($_POST['nama_user']) ? trim($_POST['nama_user']) : '';
    $address  = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $phone    = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($fullname && $address && $phone && $username && $email && $password) {
        $cek = $conn->prepare("SELECT id_user FROM user WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Username sudah digunakan. Silakan pilih yang lain.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (nama_user, alamat, no_hp, username, email, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullname, $address, $phone, $username, $email, $passwordHash);

            if ($stmt->execute()) {
                $success = "Pendaftaran berhasil. Silakan login.";
            } else {
                $error = "Pendaftaran gagal: " . $stmt->error;
            }
            $stmt->close();
        }
        $cek->close();
        $conn->close();
    } else {
        $error = "Semua field wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up - Bank Sampah</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../assets/css/signup.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="#" style="font-size: 1rem;">
                    <i class="bi bi-recycle me-1"></i>
                    <span>Bank Sampah</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
                    <ul class="navbar-nav">
                        <li class="nav-item me-1">
                            <a class="nav-link position-relative" href="../index.php">
                                <span>Home</span>
                                <span class="hover-underline"></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="login.php">
                                <span>Login</span>
                                <span class="hover-underline"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="container">
                <div class="signup-container">
                    <div class="signup-form">
                        <h2 class="text-center form-title">Daftar Akun Baru</h2>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form action="signup.php" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Nama Lengkap</label>
                                        <input type="text" name="nama_user" class="form-control form-control-custom" required>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Alamat</label>
                                        <textarea name="alamat" class="form-control form-control-custom textarea-custom" required></textarea>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">No HP</label>
                                        <input type="text" name="no_hp" class="form-control form-control-custom" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Username</label>
                                        <input type="text" name="username" class="form-control form-control-custom" required>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Email</label>
                                        <input type="email" name="email" class="form-control form-control-custom" required>
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Password</label>
                                        <input type="password" name="password" class="form-control form-control-custom" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-3">
                                <button type="submit" class="btn btn-success btn-custom">Daftar Sekarang</button>
                            </div>
                        </form>
                        
                        <p class="form-footer">Sudah punya akun? <a href="login.php">Login Sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
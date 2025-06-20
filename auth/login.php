<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi.";
    } else {
        $roles = [
            'admin' => [
                'table' => 'admin',
                'id_column' => 'id_admin',
                'redirect' => '../admin/admin_dashboard.php',
                'session_keys' => ['id_admin', 'username', 'role']
            ],
            'petugas' => [
                'table' => 'petugas',
                'id_column' => 'id_petugas',
                'redirect' => '../petugas/petugas_dashboard.php',
                'session_keys' => ['id_petugas', 'username', 'role', 'nama_petugas']
            ],
            'user' => [
                'table' => 'user',
                'id_column' => 'id_user',
                'redirect' => '../user/user_dashboard.php',
                'session_keys' => ['id_user', 'username', 'role', 'nama_user', 'email']
            ]
        ];

        $authenticated = false;
        
        foreach ($roles as $roleName => $roleConfig) {
            $stmt = $conn->prepare("SELECT * FROM {$roleConfig['table']} WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $akun = $result->fetch_assoc();
                
                if (password_verify($password, $akun['password'])) {
                    $_SESSION['user_id'] = $akun[$roleConfig['id_column']];
                    $_SESSION['username'] = $akun['username'];
                    $_SESSION['role'] = $roleName; 
                    
                    foreach ($roleConfig['session_keys'] as $key) {
                        if (isset($akun[$key])) {
                            $_SESSION[$key] = $akun[$key];
                        }
                    }
                    
                    $authenticated = true;
                    header("Location: {$roleConfig['redirect']}");
                    exit();
                }
            }
            $stmt->close();
        }

        if (!$authenticated) {
            $error = "Username atau password salah.";
            error_log("Failed login attempt for username: $username");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Bank Sampah</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../assets/css/login.css">
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
                            <a class="nav-link position-relative" href="signup.php">
                                <span>Sign Up</span>
                                <span class="hover-underline"></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="container">
                <div class="login-container">
                    <div class="login-form">
                        <h2 class="text-center form-title">Login</h2>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-custom"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group-custom">
                                <label class="form-label-custom">Username</label>
                                <input type="text" name="username" class="form-control form-control-custom" required>
                            </div>
                            <div class="form-group-custom">
                                <label class="form-label-custom">Password</label>
                                <input type="password" name="password" class="form-control form-control-custom" required>
                            </div>
                            <button type="submit" class="btn btn-success btn-custom">Login</button>
                        </form>
                        
                        <p class="form-footer">Belum punya akun? <a href="signup.php">Daftar Sekarang</a></p>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
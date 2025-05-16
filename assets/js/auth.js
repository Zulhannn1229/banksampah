document.addEventListener("DOMContentLoaded", function () {
    const path = window.location.pathname;

    // Fungsi untuk mendapatkan semua user dari localStorage
    function getUsers() {
        const users = localStorage.getItem("users");
        return users ? JSON.parse(users) : [];
    }

    // Fungsi menyimpan user baru ke localStorage
    function saveUser(user) {
        const users = getUsers();
        users.push(user);
        localStorage.setItem("users", JSON.stringify(users));
    }

    // Fungsi menyimpan sesi login
    function setLoggedInUser(user) {
        localStorage.setItem("loggedInUser", JSON.stringify(user));
    }

    // Fungsi cek user login
    function getLoggedInUser() {
        const user = localStorage.getItem("loggedInUser");
        return user ? JSON.parse(user) : null;
    }

    // Fungsi logout
    function logout() {
        localStorage.removeItem("loggedInUser");
        window.location.href = "../index.html";
    }

    // Halaman LOGIN
    if (path.includes("login.html")) {
        const form = document.querySelector("form");
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value.trim();
            const users = getUsers();

            const user = users.find(u => u.username === username && u.password === password);

            if (user) {
                setLoggedInUser(user);
                if (user.role === "admin") {
                    window.location.href = "admin/admin_dashboard.html";
                } else if (user.role === "petugas") {
                    window.location.href = "petugas/petugas_dashboard.html";
                } else {
                    window.location.href = "user/user_dashboard.html";
                }
            } else {
                alert("Username atau password salah.");
            }
        });
    }

    // Halaman SIGNUP
    if (path.includes("signup.html") || path.includes("singup.html")) {
        const form = document.querySelector("form");
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const fullname = document.getElementById("fullname").value.trim();
            const address = document.getElementById("address").value.trim();
            const phone = document.getElementById("phone").value.trim();
            const username = document.getElementById("username").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirmPassword").value;

            if (password !== confirmPassword) {
                alert("Password dan konfirmasi tidak sama.");
                return;
            }

            const users = getUsers();
            const isUsernameTaken = users.some(u => u.username === username);

            if (isUsernameTaken) {
                alert("Username sudah digunakan.");
                return;
            }

            const newUser = {
                fullname,
                address,
                phone,
                username,
                email,
                password,
                role: "user" // default role
            };

            saveUser(newUser);
            alert("Pendaftaran berhasil! Silakan login.");
            window.location.href = "login.html";
        });
    }

    // Jika tombol logout ada di halaman, tangani logout
    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", function () {
            logout();
        });
    }
});

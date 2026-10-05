<?php
session_name("asset");
session_start();
include 'config.php';

// Cek apakah user sudah login dan memiliki role Owner
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "owner") {
    echo "Akses ditolak!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password']; // Ambil password asli
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password
    $role = $_POST['role']; // Role yang dipilih oleh Owner

    // Pastikan role hanya bisa: owner, admin, atau user
    $allowed_roles = ["owner", "admin", "user"];
    if (!in_array($role, $allowed_roles)) {
        echo "<script>alert('Role tidak valid!'); window.history.back();</script>";
        exit;
    }

    // Cek apakah username sudah ada
    $check_user = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();
    $check_user->store_result();

    if ($check_user->num_rows > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
    } else {
        // Simpan ke database (dengan password_plain)
        $stmt = $conn->prepare("INSERT INTO users (username, password, password_plain, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed_password, $password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('User berhasil ditambahkan!'); window.location='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan user!'); window.history.back();</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h2 class="text-center">Tambah User Baru</h2>

        <form method="POST" action="register.php" class="p-4 border rounded bg-white">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                    <option value="owner">Owner</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah User</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
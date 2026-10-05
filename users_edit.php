<?php
session_name("asset");
session_start();
include 'config.php';

// Pastikan hanya Owner yang bisa edit user
if ($_SESSION["role"] !== "owner") {
    header("Location: users.php");
    exit;
}

// Cek apakah ID user dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID tidak valid.");
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("User tidak ditemukan.");
}

$user = $result->fetch_assoc();

// Proses update user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Jika password diisi, hash password baru dan update password_plain
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, role=?, password=?, password_plain=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $role, $hashed_password, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('User berhasil diperbarui!'); window.location='users.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate user!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2>Edit User</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                    value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="owner" <?= $user['role'] == 'owner' ? 'selected' : ''; ?>>Owner</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="users.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>

</html>
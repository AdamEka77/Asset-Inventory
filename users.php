<?php
session_name("asset");
session_start();
include 'config.php';

// Cek apakah user adalah Owner (hanya Owner yang boleh melihat daftar akun)
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "owner") {
    echo "Akses ditolak!";
    exit;
}

// Ambil data users dari database
$result = $conn->query("SELECT id, username, password, role, password_plain FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <a href="dashboard.php"><button type="button" class="btn btn-danger"><i
                    class="bi bi-caret-left-fill"></i>Kembali ke dashboard</button></a>
        <h2 class="text-center mb-4">Daftar Pengguna</h2>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Password</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['username']); ?></td>
                        <td><?= htmlspecialchars($row['role']); ?></td>
                        <td><?= htmlspecialchars($row['password_plain']); ?></td>
                        <td>
                            <a href="users_edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="users_delete.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus user ini?')">Delete</a>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
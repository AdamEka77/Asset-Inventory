<?php
session_name("asset");
session_start();
if (isset($_SESSION["username"])) {
    header("Location: dashboard.php");
    exit();
}

session_regenerate_id(true);

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    

    // Siapkan statement SQL
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    if (!$stmt) {
        die("Query error: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Cek apakah username ditemukan
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Simpan ke session
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;

            $stmt->close(); // Tutup statement

            // Arahkan ke dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-control:focus {
            box-shadow: none;
            border: 1px solid #E1252F;
        }

        body {
            background-image: url(img/login-bg.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .card {
            margin-top: 10vh;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <img src="img/logo-sumato.png" class="w-100" alt="">
                        <h4>Asset & Purchasing</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger text-center"><?= $error; ?></div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <div class="mb-3">
                                <input type="text" placeholder="Masukkan Username..." name="username"
                                    class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" placeholder="Masukkan Password..." name="password"
                                    class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
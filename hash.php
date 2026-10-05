<?php
$password = "owner123"; // Ganti dengan password yang diinginkan
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
echo "Hashed Password: " . $hashed_password;
?>
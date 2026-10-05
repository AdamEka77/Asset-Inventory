<?php
include 'config.php';

if (isset($_POST['query'])) {
    $search = "%" . $_POST['query'] . "%";
    $stmt = $conn->prepare("SELECT * FROM purchasing WHERE nama_barang LIKE ? LIMIT 5");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<ul class='list-group'>";
        while ($row = $result->fetch_assoc()) {
            echo "<li class='list-group-item pilih-barang' data-nama='{$row['nama_barang']}' data-quantity='{$row['quantity']}'>
                    <strong>{$row['nama_barang']}</strong> - Stok: {$row['quantity']}
                  </li>";
        }
        echo "</ul>";
    } else {
        echo "<ul class='list-group'><li class='list-group-item'>Barang tidak ditemukan</li></ul>";
    }
}
?>
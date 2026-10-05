<?php
session_name("asset");
session_start();
session_destroy();
header("Location: index.php");
exit();
?>
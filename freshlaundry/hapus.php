<?php
require 'cek_login.php';
require "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: pesanan.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: pesanan.php");
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM pesanan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

header("Location: pesanan.php");
exit;
?>

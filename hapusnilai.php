<?php
require "koneksi.php";

// hapus data berdasarkan nim yang dikirim dari tombol hapus
if (isset($_GET['nim'])) {
  $stmt = mysqli_prepare($conn, "DELETE FROM nilai_mhsw WHERE nim = ?");
  mysqli_stmt_bind_param($stmt, "s", $_GET['nim']);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

mysqli_close($conn);
// kembali ke halaman utama
header("Location: adminlte.php");
exit;
?>
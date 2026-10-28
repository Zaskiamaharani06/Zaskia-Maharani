<?php
require_once __DIR__ . "/koneksi.php";

$pesan = "";

// 1) Form dikirim: simpan perubahan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nim   = $_POST['nim'];
  $nama  = $_POST['nama'];
  $nilai = (float) $_POST['nilai_akhir'];
  // ambil foto yang dipilih, dikosongkan kalau alamatnya tidak valid
  $foto  = $_POST['foto'] ?? '';
  if (!preg_match('#^assets/img/[\w.\-]+$#', $foto)) { $foto = ''; }

  // Konversi nilai ke huruf & predikat (sesuaikan batasnya dengan aturan dosen)
  if ($nilai >= 85)      { $huruf = 'A'; $predikat = 'Sangat Memuaskan'; }
  elseif ($nilai >= 70)  { $huruf = 'B'; $predikat = 'Memuaskan'; }
  elseif ($nilai >= 55)  { $huruf = 'C'; $predikat = 'Cukup'; }
  elseif ($nilai >= 40)  { $huruf = 'D'; $predikat = 'Kurang'; }
  else                   { $huruf = 'E'; $predikat = 'Sangat Kurang'; }

  try {
    $stmt = mysqli_prepare($conn, "UPDATE nilai_mhsw SET nama=?, nilai_akhir=?, nilai_huruf=?, predikat=?, foto=? WHERE nim=?");
    mysqli_stmt_bind_param($stmt, "sdssss", $nama, $nilai, $huruf, $predikat, $foto, $nim);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    // kembali ke halaman utama setelah data berhasil diubah
    header("Location: adminlte.php");
    exit;
  } catch (mysqli_sql_exception $e) {
    $pesan = "Error: " . htmlspecialchars($e->getMessage());
  }

  // Jika gagal, tampilkan lagi isian yang tadi diketik
  $data = ['nim' => $nim, 'nama' => $nama, 'nilai_akhir' => $_POST['nilai_akhir']];
} else {
  // 2) Dibuka dari tombol Edit: ambil data lama
  if (!isset($_GET['nim'])) {
    header("Location: adminlte.php");
    exit;
  }
  $stmt = mysqli_prepare($conn, "SELECT nim, nama, nilai_akhir FROM nilai_mhsw WHERE nim = ?");
  mysqli_stmt_bind_param($stmt, "s", $_GET['nim']);
  mysqli_stmt_execute($stmt);
  $data = mysqli_stmt_get_result($stmt)->fetch_assoc();
  if (!$data) {
    die("Data tidak ditemukan.");
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Edit Nilai</title>
</head>
<body>
  <h3>Form Edit Nilai Mahasiswa</h3>

  <?php if ($pesan != "") { ?>
    <p style="color:red;"><?= $pesan ?></p>
  <?php } ?>

  <form method="post" action="editnilai.php">
    NIM:<br>
    <?= htmlspecialchars($data['nim']) ?>
    <input type="hidden" name="nim" value="<?= htmlspecialchars($data['nim']) ?>"><br>

    <label for="nama">Nama</label><br>
    <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($data['nama']) ?>"><br>

    <label for="nilai_akhir">Nilai Akhir</label><br>
    <input type="number" step="0.01" min="0" max="100" id="nilai_akhir" name="nilai_akhir" required
           value="<?= htmlspecialchars($data['nilai_akhir']) ?>"><br><br>

    <a href="adminlte.php"><button type="button">Cancel</button></a>
    <input type="submit" value="Update">
  </form>
</body>
</html>
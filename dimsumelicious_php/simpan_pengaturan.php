<?php
/* Menyimpan perubahan nama pengguna / kata sandi ke tabel pengguna */
require 'koneksi.php';
require 'fungsi.php';
cek_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pengaturan.php");
    exit;
}

$id_pengguna   = $_SESSION['id_pengguna'];
$nama_pengguna = mysqli_real_escape_string($koneksi, $_POST['nama_pengguna']);
$kata_sandi    = $_POST['kata_sandi'];

if (!empty($kata_sandi)) {
    $kata_sandi_aman = mysqli_real_escape_string($koneksi, $kata_sandi);
    mysqli_query($koneksi, "
        UPDATE pengguna SET nama_pengguna = '$nama_pengguna', kata_sandi = '$kata_sandi_aman'
        WHERE id_pengguna = $id_pengguna
    ");
} else {
    mysqli_query($koneksi, "
        UPDATE pengguna SET nama_pengguna = '$nama_pengguna'
        WHERE id_pengguna = $id_pengguna
    ");
}

// Perbarui session supaya nama di topbar langsung berubah
$_SESSION['nama_pengguna'] = $nama_pengguna;

header("Location: pengaturan.php?pesan=" . urlencode("Pengaturan akun berhasil disimpan."));
exit;
?>

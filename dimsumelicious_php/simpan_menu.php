<?php
/* =========================================================
   simpan_menu.php
   Memproses data dari form tambah/ubah menu di kelola_menu.php
   ========================================================= */
require 'koneksi.php';
require 'fungsi.php';
cek_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kelola_menu.php");
    exit;
}

$nama_menu = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
$kategori  = mysqli_real_escape_string($koneksi, $_POST['kategori']);
$harga     = (float) $_POST['harga'];
$status    = mysqli_real_escape_string($koneksi, $_POST['status_ketersediaan']);
$id_pengguna = $_SESSION['id_pengguna'];

if (!empty($_POST['id_menu'])) {
    // ---- UBAH data menu yang sudah ada ----
    $id_menu = (int) $_POST['id_menu'];
    mysqli_query($koneksi, "
        UPDATE menu SET
            nama_menu = '$nama_menu',
            kategori = '$kategori',
            harga = $harga,
            status_ketersediaan = '$status'
        WHERE id_menu = $id_menu
    ");
    $pesan = "Menu berhasil diperbarui.";
} else {
    // ---- TAMBAH menu baru ----
    mysqli_query($koneksi, "
        INSERT INTO menu (id_pengguna, nama_menu, harga, kategori, status_ketersediaan)
        VALUES ($id_pengguna, '$nama_menu', $harga, '$kategori', '$status')
    ");
    $pesan = "Menu baru berhasil ditambahkan.";
}

header("Location: kelola_menu.php?pesan=" . urlencode($pesan));
exit;
?>

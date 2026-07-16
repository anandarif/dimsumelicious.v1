<?php
/* Menghapus satu data menu berdasarkan id_menu */
require 'koneksi.php';
require 'fungsi.php';
cek_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    mysqli_query($koneksi, "DELETE FROM menu WHERE id_menu = $id");
}

header("Location: kelola_menu.php?pesan=" . urlencode("Menu berhasil dihapus."));
exit;
?>

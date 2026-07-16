<?php
/* Menambahkan 1 menu ke keranjang (session), atau +1 jika sudah ada */
require 'koneksi.php';
require 'fungsi.php';
cek_login();

if (!isset($_SESSION['keranjang'])) { $_SESSION['keranjang'] = []; }

$id_menu = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_menu > 0) {
    if (isset($_SESSION['keranjang'][$id_menu])) {
        $_SESSION['keranjang'][$id_menu]['jumlah']++;
    } else {
        $q = mysqli_query($koneksi, "SELECT * FROM menu WHERE id_menu = $id_menu LIMIT 1");
        $menu = mysqli_fetch_assoc($q);
        if ($menu) {
            $_SESSION['keranjang'][$id_menu] = [
                'nama'   => $menu['nama_menu'],
                'harga'  => $menu['harga'],
                'jumlah' => 1
            ];
        }
    }
}

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';
header("Location: order.php?kategori=" . urlencode($kategori));
exit;
?>

<?php
/* Mengurangi 1 jumlah menu di keranjang, hapus jika sudah 0 */
require 'fungsi.php';
cek_login();

$id_menu = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_menu > 0 && isset($_SESSION['keranjang'][$id_menu])) {
    $_SESSION['keranjang'][$id_menu]['jumlah']--;
    if ($_SESSION['keranjang'][$id_menu]['jumlah'] <= 0) {
        unset($_SESSION['keranjang'][$id_menu]);
    }
}

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';
header("Location: order.php?kategori=" . urlencode($kategori));
exit;
?>

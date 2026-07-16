<?php
/* =========================================================
   proses_pesanan.php
   Memproses tombol "Bayar Sekarang" di order.php:
   1. Simpan data pelanggan baru
   2. Simpan data pesanan
   3. Simpan rincian pesanan (detail_pesanan) per item keranjang
   4. Simpan data pembayaran
   5. Kosongkan keranjang & arahkan ke struk
   ========================================================= */
require 'koneksi.php';
require 'fungsi.php';
cek_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['keranjang'])) {
    header("Location: order.php");
    exit;
}

$id_pengguna     = $_SESSION['id_pengguna'];
$nama_pelanggan  = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggan']);
$jenis_pesanan   = mysqli_real_escape_string($koneksi, $_POST['jenis_pesanan']);
$metode_bayar    = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
$no_meja         = ($_POST['no_meja'] !== '') ? (int)$_POST['no_meja'] : 'NULL';

// 1. Simpan pelanggan baru
mysqli_query($koneksi, "INSERT INTO pelanggan (nama_pelanggan) VALUES ('$nama_pelanggan')");
$id_pelanggan = mysqli_insert_id($koneksi);

// 2. Simpan pesanan (status langsung 'Selesai' karena dibayar di halaman yang sama)
mysqli_query($koneksi, "
    INSERT INTO pesanan (id_pengguna, id_pelanggan, no_meja, jenis_pesanan, status_pesanan)
    VALUES ($id_pengguna, $id_pelanggan, $no_meja, '$jenis_pesanan', 'Selesai')
");
$no_pesanan = mysqli_insert_id($koneksi);

// 3. Simpan detail_pesanan untuk setiap item di keranjang
$total_tagihan = 0;
foreach ($_SESSION['keranjang'] as $id_menu => $item) {
    $id_menu   = (int)$id_menu;
    $jumlah    = (int)$item['jumlah'];
    $harga     = (float)$item['harga'];
    $subtotal  = $harga * $jumlah;
    $total_tagihan += $subtotal;

    mysqli_query($koneksi, "
        INSERT INTO detail_pesanan (no_pesanan, id_menu, jumlah, harga, subtotal)
        VALUES ($no_pesanan, $id_menu, $jumlah, $harga, $subtotal)
    ");
}

// Tambahkan pajak 10% ke total tagihan (mengikuti tampilan asli)
$total_tagihan = $total_tagihan + round($total_tagihan * 0.10);

// 4. Simpan data pembayaran
mysqli_query($koneksi, "
    INSERT INTO pembayaran (no_pesanan, total_tagihan, metode_pembayaran, waktu_transaksi, status_pembayaran)
    VALUES ($no_pesanan, $total_tagihan, '$metode_bayar', NOW(), 'Lunas')
");

// 5. Kosongkan keranjang
$_SESSION['keranjang'] = [];

// Arahkan ke halaman struk
header("Location: struk.php?no_pesanan=" . $no_pesanan);
exit;
?>

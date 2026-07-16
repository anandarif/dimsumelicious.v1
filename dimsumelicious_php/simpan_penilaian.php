<?php
/* Menyimpan penilaian pelanggan (rating + komentar) ke tabel evaluasi */
require 'koneksi.php';
require 'fungsi.php';
cek_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: penilaian.php");
    exit;
}

$no_pesanan = (int) $_POST['no_pesanan'];
$rating     = (int) $_POST['rating'];
$komentar   = mysqli_real_escape_string($koneksi, $_POST['komentar']);

if ($no_pesanan > 0 && $rating >= 1 && $rating <= 5) {
    mysqli_query($koneksi, "
        INSERT INTO evaluasi (no_pesanan, rating, komentar, tanggal)
        VALUES ($no_pesanan, $rating, '$komentar', CURDATE())
    ");
    $pesan = "Terima kasih, penilaian berhasil dikirim.";
} else {
    $pesan = "Pesanan atau rating tidak valid.";
}

header("Location: penilaian.php?no_pesanan=$no_pesanan&pesan=" . urlencode($pesan));
exit;
?>

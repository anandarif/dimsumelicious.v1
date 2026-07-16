<?php
/* =========================================================
   struk.php
   Menampilkan struk transaksi setelah pesanan berhasil
   dibayar, diambil langsung dari database.
   ========================================================= */
require 'koneksi.php';
require 'fungsi.php';
cek_login();

$no_pesanan = isset($_GET['no_pesanan']) ? (int)$_GET['no_pesanan'] : 0;

$q_pesanan = mysqli_query($koneksi, "
    SELECT p.*, pl.nama_pelanggan, pb.total_tagihan, pb.metode_pembayaran, pb.waktu_transaksi
    FROM pesanan p
    JOIN pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
    LEFT JOIN pembayaran pb ON pb.no_pesanan = p.no_pesanan
    WHERE p.no_pesanan = $no_pesanan
");
$pesanan = mysqli_fetch_assoc($q_pesanan);

if (!$pesanan) {
    echo "Data pesanan tidak ditemukan.";
    exit;
}

$q_item = mysqli_query($koneksi, "
    SELECT dp.*, m.nama_menu
    FROM detail_pesanan dp
    JOIN menu m ON m.id_menu = dp.id_menu
    WHERE dp.no_pesanan = $no_pesanan
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk Pesanan #<?php echo $no_pesanan; ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body style="background:var(--bg); padding:30px 0;">
  <div id="receipt-print-area">
    <div class="receipt-paper">
      <h4>Dimsumelicious</h4>
      <p class="rc-sub">Restaurant POS</p>
      <div class="rc-line"></div>
      <div class="rc-meta"><span>No. Pesanan</span><span>#<?php echo $pesanan['no_pesanan']; ?></span></div>
      <div class="rc-meta"><span>Pelanggan</span><span><?php echo amankan($pesanan['nama_pelanggan']); ?></span></div>
      <div class="rc-meta"><span>Jenis</span><span><?php echo amankan($pesanan['jenis_pesanan']); ?></span></div>
      <div class="rc-meta"><span>Waktu</span><span><?php echo amankan($pesanan['waktu_transaksi']); ?></span></div>
      <div class="rc-line"></div>
      <?php while ($item = mysqli_fetch_assoc($q_item)): ?>
        <div class="rc-item">
          <div class="rn"><span><?php echo amankan($item['nama_menu']); ?></span><span><?php echo format_rupiah($item['subtotal']); ?></span></div>
          <div class="rv"><?php echo $item['jumlah']; ?> x <?php echo format_rupiah($item['harga']); ?></div>
        </div>
      <?php endwhile; ?>
      <div class="rc-line"></div>
      <div class="rc-sum"><span>Metode Bayar</span><span><?php echo amankan($pesanan['metode_pembayaran']); ?></span></div>
      <div class="rc-total"><span>Total</span><span><?php echo format_rupiah($pesanan['total_tagihan']); ?></span></div>
      <div class="rc-foot">Terima kasih telah berkunjung 🙏</div>
    </div>
  </div>
  <div class="receipt-actions">
    <a href="order.php" style="flex:1;text-decoration:none;"><button type="button" class="close-r" style="width:100%;">Tutup</button></a>
    <button type="button" class="print-r" onclick="window.print()">🖨 Cetak</button>
  </div>
</body>
</html>

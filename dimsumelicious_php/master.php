<?php
/* =========================================================
   master.php
   Halaman "Master Menu" -> ringkasan seluruh data menu
   yang tersimpan di tabel `menu`, lengkap dengan jumlah
   terjual (dari detail_pesanan) dan rata-rata rating
   (dari evaluasi, dihitung lewat pesanan yang memuat menu itu).
   ========================================================= */
require 'koneksi.php';
$halaman_aktif = 'master';
require 'layout_atas.php';

// Total menu yang berstatus Tersedia
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM menu WHERE status_ketersediaan = 'Tersedia'");
$total_menu_aktif = mysqli_fetch_assoc($q_total)['total'];

// Pendapatan hari ini, diambil dari tabel pembayaran yang statusnya Lunas
$q_pendapatan = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(total_tagihan),0) AS total
    FROM pembayaran
    WHERE status_pembayaran = 'Lunas' AND DATE(waktu_transaksi) = CURDATE()
");
$pendapatan_hari_ini = mysqli_fetch_assoc($q_pendapatan)['total'];

// Rating rata-rata seluruh menu (dari semua evaluasi yang masuk)
$q_rating = mysqli_query($koneksi, "SELECT AVG(rating) AS rata FROM evaluasi");
$rata_rating = mysqli_fetch_assoc($q_rating)['rata'];

// Daftar menu lengkap dengan jumlah terjual & rating rata-rata per menu
// (rating dihitung dari evaluasi yang no_pesanan-nya memuat menu tersebut)
$sql_menu = "
    SELECT m.id_menu, m.nama_menu, m.kategori, m.harga, m.status_ketersediaan,
           COALESCE(SUM(dp.jumlah),0) AS terjual,
           (SELECT AVG(e.rating) FROM evaluasi e
              JOIN detail_pesanan dp2 ON dp2.no_pesanan = e.no_pesanan
              WHERE dp2.id_menu = m.id_menu) AS rata_rating
    FROM menu m
    LEFT JOIN detail_pesanan dp ON dp.id_menu = m.id_menu
    GROUP BY m.id_menu
    ORDER BY m.nama_menu ASC
";
$hasil_menu = mysqli_query($koneksi, $sql_menu);
?>

<div class="page-head">
  <div><h1>Master Menu</h1><p>Ringkasan seluruh data menu dan performa restoran hari ini.</p></div>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="row"><div><div class="label">Total Menu Aktif</div><div class="value"><?php echo $total_menu_aktif; ?></div></div><div class="icon-box">🍽️</div></div>
    <div class="trend up">↗ Semua kategori</div>
  </div>
  <div class="stat-card">
    <div class="row"><div><div class="label">Pendapatan Hari Ini</div><div class="value"><?php echo format_rupiah($pendapatan_hari_ini); ?></div></div><div class="icon-box">💰</div></div>
    <div class="trend up">↗ Berdasarkan transaksi lunas hari ini</div>
  </div>
  <div class="stat-card">
    <div class="row"><div><div class="label">Rating Rata-rata</div><div class="value"><?php echo $rata_rating ? number_format($rata_rating,1) : '-'; ?></div></div><div class="icon-box">⭐</div></div>
    <div class="trend up">↗ dari seluruh ulasan pelanggan</div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Daftar Master Menu</h3></div>
  <table>
    <tr><th>Menu</th><th>Kategori</th><th>Harga</th><th>Terjual</th><th>Rating</th><th>Status</th></tr>
    <tbody>
    <?php if (mysqli_num_rows($hasil_menu) === 0): ?>
      <tr><td colspan="6" style="text-align:center;color:var(--text-light);">Belum ada data menu. Tambahkan menu lewat halaman "Kelola Menu".</td></tr>
    <?php else: ?>
      <?php while ($baris = mysqli_fetch_assoc($hasil_menu)): ?>
        <tr>
          <td class="id"><?php echo amankan($baris['nama_menu']); ?></td>
          <td><?php echo amankan($baris['kategori']); ?></td>
          <td><?php echo format_rupiah($baris['harga']); ?></td>
          <td><?php echo $baris['terjual']; ?></td>
          <td class="stars"><?php echo $baris['rata_rating'] ? '★ '.number_format($baris['rata_rating'],1) : '-'; ?></td>
          <td>
            <?php if ($baris['status_ketersediaan'] === 'Tersedia'): ?>
              <span class="badge green">Tersedia</span>
            <?php else: ?>
              <span class="badge red">Habis</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require 'layout_bawah.php'; ?>

<?php
/* =========================================================
   laporan.php
   Laporan penjualan: total penjualan, total transaksi,
   rata-rata order, dan rincian penjualan per hari (7 hari
   terakhir), semuanya dihitung langsung dari tabel pembayaran.
   ========================================================= */
require 'koneksi.php';
$halaman_aktif = 'laporan';
require 'layout_atas.php';

// Total penjualan & transaksi (7 hari terakhir, status Lunas)
$q_ringkas = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(total_tagihan),0) AS total_penjualan, COUNT(*) AS total_transaksi
    FROM pembayaran
    WHERE status_pembayaran = 'Lunas' AND waktu_transaksi >= (CURDATE() - INTERVAL 6 DAY)
");
$ringkas = mysqli_fetch_assoc($q_ringkas);
$total_penjualan  = $ringkas['total_penjualan'];
$total_transaksi  = $ringkas['total_transaksi'];
$rata_order       = $total_transaksi > 0 ? round($total_penjualan / $total_transaksi) : 0;

// Rincian penjualan per hari, 7 hari terakhir
$q_harian = mysqli_query($koneksi, "
    SELECT DATE(waktu_transaksi) AS tanggal,
           SUM(total_tagihan) AS penjualan,
           COUNT(*) AS transaksi
    FROM pembayaran
    WHERE status_pembayaran = 'Lunas' AND waktu_transaksi >= (CURDATE() - INTERVAL 6 DAY)
    GROUP BY DATE(waktu_transaksi)
    ORDER BY tanggal ASC
");

// ---- Jika tombol "Ekspor CSV" ditekan, langsung unduh file CSV ----
if (isset($_GET['ekspor']) && $_GET['ekspor'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Laporan-Harian-Dimsumelicious-' . date('Ymd_His') . '.csv');
    $output = fopen('php://output', 'w');
    fputs($output, "\xEF\xBB\xBF"); // BOM supaya Excel baca UTF-8 dgn benar
    fputcsv($output, ['Tanggal', 'Total Penjualan (Rp)', 'Total Transaksi', 'Rata-rata Order (Rp)']);
    mysqli_data_seek($q_harian, 0);
    while ($row = mysqli_fetch_assoc($q_harian)) {
        $rata = $row['transaksi'] > 0 ? round($row['penjualan'] / $row['transaksi']) : 0;
        fputcsv($output, [$row['tanggal'], $row['penjualan'], $row['transaksi'], $rata]);
    }
    fclose($output);
    exit;
}
?>

<div class="page-head">
  <div><h1>Laporan Penjualan</h1><p>Ringkasan performa penjualan 7 hari terakhir.</p></div>
  <div class="head-actions">
    <a href="laporan.php?ekspor=csv" style="text-decoration:none;"><button class="btn dark" type="button">⬇ Ekspor Laporan (CSV)</button></a>
  </div>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="row"><div><div class="label">Total Penjualan</div><div class="value"><?php echo format_rupiah($total_penjualan); ?></div></div><div class="icon-box">💰</div></div></div>
  <div class="stat-card"><div class="row"><div><div class="label">Total Transaksi</div><div class="value"><?php echo $total_transaksi; ?></div></div><div class="icon-box">🧾</div></div></div>
  <div class="stat-card"><div class="row"><div><div class="label">Rata-rata Order</div><div class="value"><?php echo format_rupiah($rata_order); ?></div></div><div class="icon-box">📊</div></div></div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Rincian Penjualan Harian</h3></div>
  <table>
    <tr><th>Tanggal</th><th>Total Penjualan</th><th>Total Transaksi</th><th>Rata-rata Order</th></tr>
    <tbody>
    <?php if (mysqli_num_rows($q_harian) === 0): ?>
      <tr><td colspan="4" style="text-align:center;color:var(--text-light);">Belum ada transaksi pada 7 hari terakhir.</td></tr>
    <?php else: ?>
      <?php while ($row = mysqli_fetch_assoc($q_harian)):
          $rata = $row['transaksi'] > 0 ? round($row['penjualan'] / $row['transaksi']) : 0; ?>
        <tr>
          <td><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
          <td><?php echo format_rupiah($row['penjualan']); ?></td>
          <td><?php echo $row['transaksi']; ?></td>
          <td><?php echo format_rupiah($rata); ?></td>
        </tr>
      <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require 'layout_bawah.php'; ?>

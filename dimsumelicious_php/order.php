<?php
/* =========================================================
   order.php
   Halaman kasir untuk memilih menu, mengisi keranjang
   (disimpan sementara di $_SESSION), lalu checkout.
   ========================================================= */
require 'koneksi.php';
$halaman_aktif = 'order';
require 'layout_atas.php';

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = []; // id_menu => ['nama','harga','jumlah']
}

// Filter kategori (dari klik tombol kategori)
$kategori_dipilih = isset($_GET['kategori']) ? $_GET['kategori'] : 'semua';

// Ambil semua kategori yang ada di tabel menu
$daftar_kategori = [];
$q_kat = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM menu ORDER BY kategori ASC");
while ($k = mysqli_fetch_assoc($q_kat)) { $daftar_kategori[] = $k['kategori']; }

// Ambil menu yang tersedia sesuai filter kategori
if ($kategori_dipilih === 'semua') {
    $sql_menu = "SELECT * FROM menu WHERE status_ketersediaan = 'Tersedia' ORDER BY nama_menu ASC";
    $hasil_menu = mysqli_query($koneksi, $sql_menu);
} else {
    $kategori_aman = mysqli_real_escape_string($koneksi, $kategori_dipilih);
    $sql_menu = "SELECT * FROM menu WHERE status_ketersediaan = 'Tersedia' AND kategori = '$kategori_aman' ORDER BY nama_menu ASC";
    $hasil_menu = mysqli_query($koneksi, $sql_menu);
}

// Hitung subtotal, pajak (10%), dan total dari isi keranjang
$subtotal = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $subtotal += $item['harga'] * $item['jumlah'];
}
$pajak = round($subtotal * 0.10);
$total = $subtotal + $pajak;
?>

<div class="page-head">
  <div><h1>Pilih Menu</h1><p>Klik tombol + pada menu untuk menambahkannya ke keranjang.</p></div>
</div>

<div class="cat-row">
  <a class="cat-btn <?php echo $kategori_dipilih==='semua'?'active':''; ?>" href="order.php?kategori=semua">Semua</a>
  <?php foreach ($daftar_kategori as $kat): ?>
    <a class="cat-btn <?php echo $kategori_dipilih===$kat?'active':''; ?>" href="order.php?kategori=<?php echo urlencode($kat); ?>"><?php echo amankan($kat); ?></a>
  <?php endforeach; ?>
</div>

<?php if (isset($_GET['pesan'])): ?>
  <div class="panel" style="border-left:4px solid var(--green); padding:14px 18px;">
    <?php echo amankan($_GET['pesan']); ?>
  </div>
<?php endif; ?>

<div class="order-layout">
  <div class="order-main">
    <div class="menu-grid">
      <?php if (mysqli_num_rows($hasil_menu) === 0): ?>
        <p style="color:var(--text-light);">Tidak ada menu tersedia pada kategori ini.</p>
      <?php else: ?>
        <?php while ($menu = mysqli_fetch_assoc($hasil_menu)): ?>
          <div class="menu-card">
            <div class="img" style="background:linear-gradient(135deg,#f6c518,#e14b4b); display:flex; align-items:center; justify-content:center; font-size:32px;">🥟</div>
            <div class="body">
              <div class="name"><?php echo amankan($menu['nama_menu']); ?></div>
              <div class="rating-line"><?php echo amankan($menu['kategori']); ?></div>
              <div class="price-row">
                <span class="price"><?php echo format_rupiah($menu['harga']); ?></span>
                <a href="keranjang_tambah.php?id=<?php echo $menu['id_menu']; ?>&kategori=<?php echo urlencode($kategori_dipilih); ?>" class="add-btn" style="display:flex;align-items:center;justify-content:center;text-decoration:none;">+</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="order-side">
    <h3>Pesanan Aktif</h3>
    <div class="oid">Keranjang Sementara</div>

    <div id="cart-list">
      <?php if (empty($_SESSION['keranjang'])): ?>
        <div class="cart-empty">Belum ada item dipilih.<br>Klik + pada menu untuk menambahkan.</div>
      <?php else: ?>
        <?php foreach ($_SESSION['keranjang'] as $id_menu => $item): ?>
          <div class="cart-item">
            <div class="info">
              <div class="n"><?php echo amankan($item['nama']); ?></div>
              <div class="p"><?php echo format_rupiah($item['harga']); ?> x <?php echo $item['jumlah']; ?></div>
            </div>
            <div class="qty-ctrl">
              <a href="keranjang_kurang.php?id=<?php echo $id_menu; ?>&kategori=<?php echo urlencode($kategori_dipilih); ?>" style="text-decoration:none;"><button type="button">-</button></a>
              <span><?php echo $item['jumlah']; ?></span>
              <a href="keranjang_tambah.php?id=<?php echo $id_menu; ?>&kategori=<?php echo urlencode($kategori_dipilih); ?>" style="text-decoration:none;"><button type="button" class="plus">+</button></a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div style="margin-top:10px;">
      <div class="sum-row"><span>Subtotal</span><span><?php echo format_rupiah($subtotal); ?></span></div>
      <div class="sum-row"><span>Pajak (10%)</span><span><?php echo format_rupiah($pajak); ?></span></div>
      <div class="sum-row total"><span>Total Bayar</span><span><?php echo format_rupiah($total); ?></span></div>
    </div>

    <?php if (!empty($_SESSION['keranjang'])): ?>
      <form method="POST" action="proses_pesanan.php">
        <div class="fgroup" style="margin-top:12px;">
          <label>Nama Pelanggan</label>
          <input type="text" name="nama_pelanggan" placeholder="Contoh: Andi" required>
        </div>
        <div class="fgrid2">
          <div class="fgroup">
            <label>Jenis Pesanan</label>
            <select name="jenis_pesanan">
              <option value="Makan di Tempat">Makan di Tempat</option>
              <option value="Bawa Pulang">Bawa Pulang</option>
            </select>
          </div>
          <div class="fgroup">
            <label>No. Meja (opsional)</label>
            <input type="number" name="no_meja" placeholder="Contoh: 4">
          </div>
        </div>
        <div class="fgroup">
          <label>Metode Pembayaran</label>
          <select name="metode_pembayaran">
            <option value="Tunai">Tunai</option>
            <option value="Debit/Kredit">Debit / Kredit</option>
            <option value="QRIS">QRIS</option>
            <option value="E-Wallet">E-Wallet</option>
          </select>
        </div>
        <button class="pay-btn" type="submit">💰 Bayar Sekarang</button>
      </form>
      <div class="mini-actions">
        <a href="keranjang_hapus_semua.php" style="text-decoration:none;width:100%;"><button type="button" style="width:100%;">🗑 Kosongkan Keranjang</button></a>
      </div>
    <?php else: ?>
      <button class="pay-btn" disabled>💰 Bayar Sekarang</button>
    <?php endif; ?>
  </div>
</div>

<?php require 'layout_bawah.php'; ?>

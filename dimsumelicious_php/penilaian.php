<?php
/* =========================================================
   penilaian.php
   Halaman untuk memberi & melihat penilaian pelanggan.
   Catatan: pada tabel `evaluasi`, penilaian tersimpan per
   NOMOR PESANAN (no_pesanan) -- bukan per menu -- karena
   begitulah struktur tabel evaluasi pada database.
   ========================================================= */
require 'koneksi.php';
$halaman_aktif = 'penilaian';
require 'layout_atas.php';

// Daftar pesanan yang sudah selesai, untuk dipilih sebagai objek penilaian
$q_pesanan = mysqli_query($koneksi, "
    SELECT p.no_pesanan, pl.nama_pelanggan
    FROM pesanan p
    JOIN pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
    WHERE p.status_pesanan = 'Selesai'
    ORDER BY p.no_pesanan DESC
");

// Pesanan mana yang sedang dilihat ulasannya (default: yang terbaru)
$no_pesanan_dipilih = isset($_GET['no_pesanan']) ? (int)$_GET['no_pesanan'] : 0;

$q_ulasan = mysqli_query($koneksi, "
    SELECT * FROM evaluasi
    WHERE no_pesanan = $no_pesanan_dipilih
    ORDER BY id_evaluasi DESC
");
?>

<div class="page-head">
  <div><h1>Penilaian Pelanggan</h1><p>Rating dan kritik/saran dari pelanggan untuk setiap pesanan.</p></div>
</div>

<?php if (isset($_GET['pesan'])): ?>
  <div class="panel" style="border-left:4px solid var(--green); padding:14px 18px;">
    <?php echo amankan($_GET['pesan']); ?>
  </div>
<?php endif; ?>

<div class="rating-layout">
  <div class="panel" style="flex:1; min-width:300px;">
    <div class="panel-head"><h3>Beri Penilaian</h3></div>
    <form method="POST" action="simpan_penilaian.php">
      <div class="fgroup">
        <label>Pilih Nomor Pesanan</label>
        <select name="no_pesanan" required>
          <option value="">-- pilih pesanan --</option>
          <?php while ($p = mysqli_fetch_assoc($q_pesanan)): ?>
            <option value="<?php echo $p['no_pesanan']; ?>">#<?php echo $p['no_pesanan']; ?> - <?php echo amankan($p['nama_pelanggan']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="fgroup">
        <label>Rating</label>
        <select name="rating" required>
          <option value="5">★★★★★ (5)</option>
          <option value="4">★★★★ (4)</option>
          <option value="3">★★★ (3)</option>
          <option value="2">★★ (2)</option>
          <option value="1">★ (1)</option>
        </select>
      </div>
      <div class="fgroup">
        <label>Kritik & Saran</label>
        <textarea name="komentar" placeholder="Bagaimana rasa, penyajian, atau pelayanannya?"></textarea>
      </div>
      <button class="btn-save-setting" style="width:100%;" type="submit">📝 Kirim Penilaian</button>
    </form>
  </div>

  <div class="panel" style="flex:1.3; min-width:320px;">
    <div class="panel-head"><h3>Lihat Ulasan per Pesanan</h3></div>
    <div class="fgroup">
      <form method="GET" action="penilaian.php">
        <label>Pilih Nomor Pesanan</label>
        <select name="no_pesanan" onchange="this.form.submit()">
          <option value="0">-- pilih pesanan --</option>
          <?php
          mysqli_data_seek($q_pesanan, 0);
          while ($p = mysqli_fetch_assoc($q_pesanan)): ?>
            <option value="<?php echo $p['no_pesanan']; ?>" <?php echo $no_pesanan_dipilih==$p['no_pesanan']?'selected':''; ?>>#<?php echo $p['no_pesanan']; ?> - <?php echo amankan($p['nama_pelanggan']); ?></option>
          <?php endwhile; ?>
        </select>
      </form>
    </div>
    <div id="review-list">
      <?php if ($no_pesanan_dipilih === 0): ?>
        <div class="cart-empty">Pilih nomor pesanan untuk melihat ulasannya.</div>
      <?php elseif (mysqli_num_rows($q_ulasan) === 0): ?>
        <div class="cart-empty">Belum ada ulasan untuk pesanan ini.</div>
      <?php else: ?>
        <?php while ($u = mysqli_fetch_assoc($q_ulasan)): ?>
          <div class="review-card">
            <div class="rh"><b class="stars"><?php echo str_repeat('★', $u['rating']) . str_repeat('☆', 5-$u['rating']); ?></b><span class="rdate"><?php echo amankan($u['tanggal']); ?></span></div>
            <div class="rtext"><?php echo amankan($u['komentar']); ?></div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require 'layout_bawah.php'; ?>

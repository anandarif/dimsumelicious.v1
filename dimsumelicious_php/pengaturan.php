<?php
/* =========================================================
   pengaturan.php
   Halaman pengaturan akun: ubah nama pengguna & kata sandi
   (data tersimpan di tabel `pengguna`).
   ========================================================= */
require 'koneksi.php';
$halaman_aktif = 'pengaturan';
require 'layout_atas.php';

$id_pengguna = $_SESSION['id_pengguna'];
$q = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE id_pengguna = $id_pengguna");
$data_pengguna = mysqli_fetch_assoc($q);
?>

<div class="page-head">
  <div><h1>Pengaturan Akun</h1><p>Ubah nama pengguna dan kata sandi akun Anda.</p></div>
</div>

<?php if (isset($_GET['pesan'])): ?>
  <div class="panel" style="border-left:4px solid var(--green); padding:14px 18px;">
    <?php echo amankan($_GET['pesan']); ?>
  </div>
<?php endif; ?>

<div class="panel" style="max-width:480px;">
  <div class="avatar-row">
    <div class="avatar-big"></div>
    <div>
      <div style="font-weight:700;"><?php echo amankan($data_pengguna['nama_pengguna']); ?></div>
      <div style="font-size:12px;color:var(--text-mid);">Admin Dimsumelicious POS</div>
    </div>
  </div>

  <form method="POST" action="simpan_pengaturan.php">
    <div class="fgroup">
      <label>Nama Pengguna</label>
      <input type="text" name="nama_pengguna" required value="<?php echo amankan($data_pengguna['nama_pengguna']); ?>">
    </div>
    <div class="fgroup">
      <label>Kata Sandi Baru (kosongkan jika tidak diubah)</label>
      <input type="password" name="kata_sandi" placeholder="••••••••">
    </div>
    <button class="btn-save-setting" type="submit">💾 Simpan Perubahan</button>
  </form>
</div>

<?php require 'layout_bawah.php'; ?>

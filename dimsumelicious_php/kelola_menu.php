<?php
/* =========================================================
   kelola_menu.php
   Halaman untuk menambah, mengubah, dan menghapus data
   menu di tabel `menu`.
   ========================================================= */
require 'koneksi.php';
$halaman_aktif = 'kelola';
require 'layout_atas.php';

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$menu_diedit = null;

if ($aksi === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $q = mysqli_query($koneksi, "SELECT * FROM menu WHERE id_menu = $id");
    $menu_diedit = mysqli_fetch_assoc($q);
}

$hasil_menu = mysqli_query($koneksi, "SELECT * FROM menu ORDER BY id_menu DESC");
?>

<div class="page-head">
  <div><h1>Pengelola Menu</h1><p>Tambah, ubah, atau hapus data menu restoran Anda.</p></div>
  <div class="head-actions">
    <a href="kelola_menu.php?aksi=tambah" style="text-decoration:none;"><button class="btn yellow" type="button">+ Tambah Menu Baru</button></a>
  </div>
</div>

<?php if (isset($_GET['pesan'])): ?>
  <div class="panel" style="border-left:4px solid var(--green); padding:14px 18px;">
    <?php echo amankan($_GET['pesan']); ?>
  </div>
<?php endif; ?>

<?php if ($aksi === 'tambah' || $aksi === 'edit'): ?>
  <!-- ================= FORM TAMBAH / EDIT MENU ================= -->
  <div class="panel" style="max-width:520px;">
    <div class="panel-head"><h3><?php echo $menu_diedit ? 'Ubah Menu' : 'Tambah Menu Baru'; ?></h3></div>
    <form method="POST" action="simpan_menu.php">
      <?php if ($menu_diedit): ?>
        <input type="hidden" name="id_menu" value="<?php echo $menu_diedit['id_menu']; ?>">
      <?php endif; ?>

      <div class="fgroup">
        <label>Nama Makanan</label>
        <input type="text" name="nama_menu" placeholder="Contoh: Hakau Udang" required
               value="<?php echo $menu_diedit ? amankan($menu_diedit['nama_menu']) : ''; ?>">
      </div>
      <div class="fgrid2">
        <div class="fgroup">
          <label>Kategori</label>
          <input type="text" name="kategori" placeholder="Contoh: Dimsum Mentai" required
                 value="<?php echo $menu_diedit ? amankan($menu_diedit['kategori']) : ''; ?>">
        </div>
        <div class="fgroup">
          <label>Harga (Rp)</label>
          <input type="number" name="harga" placeholder="Contoh: 15000" required
                 value="<?php echo $menu_diedit ? $menu_diedit['harga'] : ''; ?>">
        </div>
      </div>
      <div class="fgroup">
        <label>Status Ketersediaan</label>
        <select name="status_ketersediaan">
          <option value="Tersedia" <?php echo ($menu_diedit && $menu_diedit['status_ketersediaan']==='Tersedia') ? 'selected' : ''; ?>>Tersedia</option>
          <option value="Habis" <?php echo ($menu_diedit && $menu_diedit['status_ketersediaan']==='Habis') ? 'selected' : ''; ?>>Habis</option>
        </select>
      </div>

      <div class="form-foot" style="padding:0; margin-top:10px;">
        <a href="kelola_menu.php" style="flex:1;text-decoration:none;"><button type="button" class="cancel" style="width:100%;">Batal</button></a>
        <button class="save" type="submit" style="flex:1;">Simpan Menu</button>
      </div>
    </form>
  </div>
<?php endif; ?>

<!-- ================= DAFTAR MENU ================= -->
<div class="inv-grid">
  <?php if (mysqli_num_rows($hasil_menu) === 0): ?>
    <p style="color:var(--text-light);">Belum ada data menu.</p>
  <?php else: ?>
    <?php while ($m = mysqli_fetch_assoc($hasil_menu)): ?>
      <div class="inv-card">
        <div class="img" style="background:linear-gradient(135deg,#f6c518,#e14b4b); display:flex; align-items:center; justify-content:center; font-size:34px;">🥟
          <?php if ($m['status_ketersediaan']==='Tersedia'): ?>
            <span class="avail-pill tersedia"><span class="dotpulse"></span>Tersedia</span>
          <?php else: ?>
            <span class="avail-pill habis"><span class="dotpulse"></span>Habis</span>
          <?php endif; ?>
        </div>
        <div class="body">
          <div class="top-row"><div class="name"><?php echo amankan($m['nama_menu']); ?></div><div class="price"><?php echo format_rupiah($m['harga']); ?></div></div>
          <div class="desc">Kategori: <?php echo amankan($m['kategori']); ?></div>
          <div class="inv-actions">
            <a href="kelola_menu.php?aksi=edit&id=<?php echo $m['id_menu']; ?>" style="flex:1;text-decoration:none;"><button type="button" style="width:100%;">✏️ Ubah</button></a>
            <a href="hapus_menu.php?id=<?php echo $m['id_menu']; ?>" style="flex:1;text-decoration:none;" onclick="return confirm('Yakin ingin menghapus menu ini?');"><button type="button" class="del" style="width:100%;">🗑 Hapus</button></a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>

<?php require 'layout_bawah.php'; ?>

<?php
/* =========================================================
   layout_atas.php
   Bagian atas tampilan (sidebar kiri + topbar) yang SAMA
   di semua halaman setelah login. Supaya tidak menulis
   ulang kode HTML yang sama di setiap file.

   Cara pakai di halaman lain:
     $halaman_aktif = 'order';   // nama menu yang sedang aktif
     require 'layout_atas.php';
     ... isi konten halaman ...
     require 'layout_bawah.php';
   ========================================================= */
require_once 'fungsi.php';
cek_login(); // halaman ini hanya boleh diakses jika sudah login

if (!isset($halaman_aktif)) { $halaman_aktif = ''; }

function kelas_aktif($nama, $halaman_aktif) {
    return $nama === $halaman_aktif ? 'nav-item active' : 'nav-item';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dimsumelicious POS</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div id="app" style="display:block;">
  <div class="shell">
    <div class="sidebar">
      <div class="brand">
        <div class="logo-img"><img src="https://api.dicebear.com/7.x/icons/svg?icon=bowl-food" alt="Logo"></div>
        <div><h2>Dimsumelicious</h2><p>Restaurant POS</p></div>
      </div>
      <a class="<?php echo kelas_aktif('master', $halaman_aktif); ?>" href="master.php"><span class="ic">📋</span><span class="lbl">Master Menu</span></a>
      <a class="<?php echo kelas_aktif('order', $halaman_aktif); ?>" href="order.php"><span class="ic">🧾</span><span class="lbl">Order</span></a>
      <a class="<?php echo kelas_aktif('kelola', $halaman_aktif); ?>" href="kelola_menu.php"><span class="ic">📦</span><span class="lbl">Kelola Menu</span></a>
      <a class="<?php echo kelas_aktif('penilaian', $halaman_aktif); ?>" href="penilaian.php"><span class="ic">⭐</span><span class="lbl">Penilaian</span></a>
      <a class="<?php echo kelas_aktif('laporan', $halaman_aktif); ?>" href="laporan.php"><span class="ic">📈</span><span class="lbl">Laporan</span></a>
      <a class="<?php echo kelas_aktif('pengaturan', $halaman_aktif); ?>" href="pengaturan.php"><span class="ic">⚙️</span><span class="lbl">Pengaturan</span></a>
      <div class="sidebar-bottom">
        <a class="nav-item" href="logout.php"><span class="ic">⏻</span><span class="lbl">Keluar</span></a>
      </div>
    </div>

    <div class="main">
      <div class="topbar">
        <div class="search-box">🔍 <input placeholder="Cari menu, kategori, atau kode..."></div>
        <div class="topbar-right">
          <div class="icon-btn">🔔<span class="dot"></span></div>
          <div class="icon-btn">❓</div>
          <div class="user-chip">
            <div class="av"></div>
            <div><div class="name"><?php echo amankan($_SESSION['nama_pengguna']); ?></div><div class="role">Admin</div></div>
          </div>
        </div>
      </div>

      <div class="content">

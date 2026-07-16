<?php
/* =========================================================
   login.php
   Halaman login. Data dicek langsung ke tabel `pengguna`
   di database db_dimsumelicious.
   ========================================================= */
require 'koneksi.php';
require 'fungsi.php';

$pesan_error = "";

// Jika tombol "Masuk Sekarang" ditekan (form dikirim)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pengguna = mysqli_real_escape_string($koneksi, $_POST['username']);
    $kata_sandi    = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Cari data pengguna berdasarkan nama_pengguna
    $sql  = "SELECT * FROM pengguna WHERE nama_pengguna = '$nama_pengguna' LIMIT 1";
    $hasil = mysqli_query($koneksi, $sql);

    if ($hasil && mysqli_num_rows($hasil) === 1) {
        $data = mysqli_fetch_assoc($hasil);

        // Cocokkan kata sandi (disimpan polos sesuai struktur tabel pengguna)
        if ($kata_sandi === $data['kata_sandi']) {
            // Login berhasil -> simpan data ke session
            $_SESSION['id_pengguna']   = $data['id_pengguna'];
            $_SESSION['nama_pengguna'] = $data['nama_pengguna'];
            header("Location: order.php");
            exit;
        } else {
            $pesan_error = "Nama pengguna atau kata sandi salah. Silakan coba lagi.";
        }
    } else {
        $pesan_error = "Nama pengguna atau kata sandi salah. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dimsumelicious POS - Login</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div id="login-screen" style="display:flex;">
  <div class="login-deco" style="top:30px;left:40px;">🥢</div>
  <div class="login-deco" style="bottom:30px;right:50px;">🥟</div>
  <div class="login-box">
    <div class="login-logo"><img id="login-logo-img" src="https://api.dicebear.com/7.x/icons/svg?icon=bowl-food" alt="Logo Dimsumelicious"></div>
    <h1>Masuk ke POS</h1>
    <p class="sub">Kelola pesanan restoran Anda dengan mudah</p>

    <?php if ($pesan_error): ?>
      <div id="login-error" style="display:block;"><?php echo amankan($pesan_error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="field">
        <label>👤 Nama Pengguna</label>
        <input name="username" type="text" placeholder="Masukkan nama pengguna" required>
      </div>
      <div class="field">
        <label>🔒 Kata Sandi <span class="link" onclick="alert('Silakan hubungi admin untuk atur ulang kata sandi.')">Lupa kata sandi?</span></label>
        <div class="pw-wrap">
          <input name="password" type="password" placeholder="••••••••" required>
        </div>
      </div>
      <label class="remember"><input type="checkbox" style="width:auto;"> Ingat saya di perangkat ini</label>
      <button class="btn-login" type="submit">Masuk Sekarang →</button>
    </form>

    <div class="login-foot">© 2026 Dimsumelicious POS. Versi 3.0 (PHP + MySQL)</div>
    <div class="login-links"><span>❓ Bantuan</span><span>🌐 Bahasa Indonesia</span><span>✉️ Hubungi Admin</span></div>
  </div>
</div>
</body>
</html>

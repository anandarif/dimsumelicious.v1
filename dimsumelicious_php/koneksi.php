<?php
/* =========================================================
   koneksi.php
   File ini bertugas menghubungkan seluruh halaman PHP
   ke database MySQL "db_dimsumelicious".

   Cara pakai:
   - Cukup letakkan file ini satu folder dengan file PHP lain
   - Setiap halaman lain tinggal menulis: require 'koneksi.php';
   ========================================================= */

// ---- Pengaturan koneksi database ----
// Silakan sesuaikan jika username/password MySQL di komputer Anda berbeda
$host     = "localhost";
$user_db  = "root";
$pass_db  = "";
$nama_db  = "db_dimsumelicious";

// Membuat koneksi ke MySQL menggunakan MySQLi (cara paling dasar di PHP)
$koneksi = mysqli_connect($host, $user_db, $pass_db, $nama_db);

// Jika koneksi gagal, hentikan program dan tampilkan pesan error
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Supaya karakter (huruf, emoji, dsb) tersimpan dan tampil dengan benar
mysqli_set_charset($koneksi, "utf8mb4");
?>

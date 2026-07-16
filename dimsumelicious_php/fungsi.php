<?php
/* =========================================================
   fungsi.php
   Kumpulan fungsi bantu yang dipakai berulang-ulang
   di banyak halaman, supaya kode tidak ditulis berkali-kali.
   ========================================================= */

// Selalu mulai session di setiap halaman yang membutuhkan login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Format angka menjadi format uang Rupiah, contoh: 15000 -> Rp 15.000
function format_rupiah($angka) {
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

// Mengecek apakah pengguna sudah login. Jika belum, tendang ke halaman login.
function cek_login() {
    if (!isset($_SESSION['id_pengguna'])) {
        header("Location: login.php");
        exit;
    }
}

// Membersihkan input dari user sebelum ditampilkan lagi ke HTML (anti XSS sederhana)
function amankan($teks) {
    return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
}
?>

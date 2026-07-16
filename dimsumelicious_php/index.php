<?php
/* =========================================================
   index.php
   Pintu masuk aplikasi.
   - Jika sudah login -> langsung ke halaman Order
   - Jika belum login -> ke halaman Login
   ========================================================= */
require 'fungsi.php';

if (isset($_SESSION['id_pengguna'])) {
    header("Location: order.php");
} else {
    header("Location: login.php");
}
exit;
?>

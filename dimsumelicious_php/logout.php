<?php
/* =========================================================
   logout.php
   Menghapus session (mengeluarkan pengguna) lalu
   mengarahkan kembali ke halaman login.
   ========================================================= */
require 'fungsi.php';

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>

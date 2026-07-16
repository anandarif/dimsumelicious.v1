<?php
/* Mengosongkan seluruh isi keranjang */
require 'fungsi.php';
cek_login();

$_SESSION['keranjang'] = [];

header("Location: order.php");
exit;
?>

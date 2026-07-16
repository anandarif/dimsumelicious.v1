# Dimsumelicious POS — Versi PHP + MySQL

Konversi dari `index.html` (HTML/CSS/JS statis) menjadi aplikasi PHP
dasar yang terhubung langsung ke database `db_dimsumelicious`.
Tampilan (CSS asli) **tidak diubah** — hanya dipisah ke `assets/style.css`
dan sekarang diisi data sungguhan dari database, bukan data contoh (dummy).

## 1. Cara Instalasi (XAMPP/Laragon)

1. Copy seluruh folder `dimsumelicious_php` ke folder `htdocs` (XAMPP)
   atau `www` (Laragon).
2. Buka phpMyAdmin, buat database baru bernama **db_dimsumelicious**.
3. Import file `db_dimsumelicious.sql` (struktur tabel) — file ini yang
   Anda unggah sebelumnya.
4. Import juga file `akun_admin.sql` yang disertakan di sini, supaya ada
   akun untuk login pertama kali.
5. Buka `koneksi.php`, sesuaikan `$user_db` / `$pass_db` jika MySQL Anda
   memakai username/password berbeda dari `root` / kosong.
6. Buka browser ke `http://localhost/dimsumelicious_php/`.
7. Login dengan:
   - Nama Pengguna: `admin`
   - Kata Sandi: `admin123`

## 2. Daftar File

| File | Fungsi |
|---|---|
| `koneksi.php` | Koneksi ke database MySQL (mysqli) |
| `fungsi.php` | Fungsi bantu: format Rupiah, cek login, dsb |
| `login.php` | Halaman & proses login (cek ke tabel `pengguna`) |
| `logout.php` | Proses keluar / hapus session |
| `layout_atas.php` / `layout_bawah.php` | Kerangka sidebar & topbar yang dipakai bersama semua halaman |
| `master.php` | Master Menu — ringkasan menu, pendapatan, rating |
| `order.php` | Order — pilih menu, keranjang, checkout |
| `keranjang_tambah.php`, `keranjang_kurang.php`, `keranjang_hapus_semua.php` | Kelola isi keranjang (session) |
| `proses_pesanan.php` | Simpan pesanan ke `pesanan`, `detail_pesanan`, `pembayaran` |
| `struk.php` | Cetak struk transaksi |
| `kelola_menu.php`, `simpan_menu.php`, `hapus_menu.php` | CRUD data menu |
| `penilaian.php`, `simpan_penilaian.php` | Beri & lihat penilaian (tabel `evaluasi`) |
| `laporan.php` | Laporan penjualan 7 hari terakhir + ekspor CSV |
| `pengaturan.php`, `simpan_pengaturan.php` | Ubah nama pengguna / kata sandi |
| `index.php` | Pintu masuk, mengarahkan ke login/order |
| `assets/style.css` | CSS asli dari `index.html`, dipakai semua halaman |

## 3. Penyesuaian dari Tampilan Asli

`index.html` aslinya adalah aplikasi **front-end murni** (semua data
seperti menu, rasa, paket, topping, foto, meja, dsb hanya data contoh
di JavaScript, tidak tersambung ke database apa pun). Sedangkan struktur
tabel di `db_dimsumelicious.sql` jauh lebih sederhana. Agar aplikasi ini
benar-benar berjalan dengan data asli dari database (bukan cuma tampilan
kosong), ada beberapa penyesuaian **fungsi** (bukan tampilan/CSS):

- **Rasa, ukuran paket, topping, dan foto menu** dihilangkan karena
  tabel `menu` tidak memiliki kolom-kolom tersebut (hanya
  `nama_menu`, `harga`, `kategori`, `status_ketersediaan`).
- **Penilaian (evaluasi)** diberikan per **Nomor Pesanan**, bukan per
  menu — karena tabel `evaluasi` hanya punya `no_pesanan`, bukan `id_menu`.
- **Denah/pemilihan meja visual** disederhanakan menjadi input nomor
  meja biasa, karena tidak ada tabel meja di database.
- Grafik batang/pie interaktif pada laporan disederhanakan menjadi tabel
  ringkas per hari (data tetap 100% asli dari tabel `pembayaran`), agar
  kode tetap sesederhana mungkin sesuai permintaan.
- Struk & metode pembayaran tetap ada dan tersimpan ke tabel `pembayaran`.

Seluruh warna, font, kartu, tombol, sidebar, dan tata letak halaman
**sama persis** dengan `index.html` asli karena memakai file CSS yang
sama tanpa perubahan.

## 4. Catatan Keamanan

Kata sandi pengguna disimpan **polos (tanpa enkripsi)** mengikuti
struktur kolom `kata_sandi varchar(30)` pada tabel `pengguna`. Ini
dipertahankan supaya kode tetap sederhana dan mudah dipahami (sesuai
permintaan). Untuk aplikasi produksi sungguhan, sebaiknya gunakan
`password_hash()` dan `password_verify()`.

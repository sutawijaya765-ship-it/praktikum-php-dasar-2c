# Mini Digital Library

Aplikasi administrasi perpustakaan mini dengan PHP Native 8+, MySQL/MariaDB, PDO, Bootstrap 5, HTML, CSS, dan JavaScript.

## Instalasi XAMPP

1. Letakkan folder project di `C:\xampp2\htdocs\project_perpustakaan`.
2. Di XAMPP Control Panel, jalankan Apache dan MySQL.
3. Buka `http://localhost/phpmyadmin`, pilih **Import**, dan impor `database/mini_digital_library.sql`. File tersebut membuat database beserta tabel dan data uji.
   Jika aplikasi sudah terpasang sebelumnya, jalankan juga `database/security_migration.sql` sekali untuk membuat tabel pembatasan login.
4. Periksa `config/config.php`. Nilai bawaan memakai host `127.0.0.1`, database `mini_digital_library`, user `root`, dan password kosong. Sesuaikan jika konfigurasi MySQL berbeda.
5. Pastikan Apache dapat menulis ke folder `uploads/` untuk sampul buku.
6. Jika diperlukan, jalankan seeder simulasi dari CLI di folder project: `php database/seed_demo.php`. Seeder tidak dapat dijalankan melalui URL web.
7. Buka `http://localhost/project_perpustakaan/`.

### SQL untuk pemeriksaan dosen

Pada halaman login, pilih **Unduh SQL demonstrasi**, atau buka `http://localhost/project_perpustakaan/?page=database_download`. File publik membuat database terpisah bernama `mini_digital_library_demo` dengan struktur tabel dan data contoh, tanpa baris akun pengguna atau hash password. Di phpMyAdmin, impor file yang diunduh ke database demo tersebut agar dapat meninjau struktur dan data.

`database/mini_digital_library.sql` tetap dipertahankan sebagai dump instalasi lokal asli. Folder `database/` diblokir dari akses HTTP langsung; situs hanya menyajikan salinan publik yang sudah disanitasi. Jangan membagikan dump asli karena berisi hash akun seed.

Pada instalasi pengembangan, akun admin seed tetap tersedia dan password-nya disimpan sebagai hash bcrypt. Login pertama dengan password seed wajib mengganti password sebelum halaman admin lain dapat dibuka. Jangan deploy aplikasi dengan password seed atau kredensial MySQL bawaan; gunakan kata sandi unik dan aktifkan HTTPS.

## Fitur yang tersedia

- Login/logout admin, penggantian kata sandi (minimal 12 karakter), password hash, validasi, dan CSRF pada aksi perubahan. Penggunaan password seed memaksa perubahan sebelum akses admin.
- Login dibatasi hingga 5 percobaan gagal per alamat IP dalam periode 15 menit; setelah batas tercapai, login dibuka kembali ketika masa tunggu berakhir.
- Seeder idempotent berisi 8 kategori demo, 50 buku demo dengan sampul SVG, 25 anggota fiktif, serta 12 transaksi (6 dipinjam dan 6 dikembalikan). Data anggota memakai domain `example.test`. Seeder mempertahankan akun admin, menghindari duplikasi dengan ISBN/nomor anggota/kunci transaksi, dan hanya mengurangi stok satu kali untuk peminjaman aktif.
- Dashboard statistik buku, kategori, anggota, peminjaman aktif dan keterlambatan, grafik peminjaman bulanan, serta aktivitas terbaru.
- Buku: tambah, detail, edit, hapus, pencarian judul/penulis/ISBN, filter kategori, kartu koleksi dengan sampul, serta unggah cover JPG/PNG/WEBP maksimal 2 MB.
- Kategori: tambah, edit langsung pada formulir di tabel, dan hapus. Penghapusan kategori yang masih memiliki buku ditolak oleh relasi database.
- Anggota: tambah, edit seluruh kolom (nomor, nama, email, telepon, alamat), dan hapus.
- Peminjaman satu jenis buku per transaksi, kuantitas, tanggal pinjam dan jatuh tempo. Stok diperiksa dan dikurangi dalam transaksi database.
- Pengembalian mencatat tanggal kembali, mengubah status, menghitung keterlambatan, dan mengembalikan stok.
- Riwayat transaksi dan laporan yang bisa difilter berdasarkan tanggal peminjaman.
- Antarmuka Bahasa Indonesia responsif dengan Bootstrap 5 dari CDN.

## Konfigurasi dan keamanan

Kredensial database dan `APP_SECRET` diatur dalam `config/config.php`; jaga file ini tetap privat. Akses web langsung ke folder `config/`, `database/`, dan `includes/` ditolak oleh `.htaccess` pada Apache. Query memakai PDO prepared statements. Input keluaran di-escape, aksi perubahan memakai token CSRF, dan file unggahan diperiksa MIME, isi gambar, serta ukuran. Folder uploads juga membatasi eksekusi file PHP melalui `.htaccess`. Error koneksi database dicatat di log server dan pengguna mendapat pesan layanan sementara.

## Status pemeriksaan

Lint PHP dapat dijalankan dengan `Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }`. Setelah menjalankan seeder, pemeriksaan lokal menunjukkan 8 kategori, 50 buku demo (52 buku total termasuk 2 data awal), 25 anggota demo (27 total termasuk 2 data awal), dan 12 transaksi demo. Seeder dijalankan dua kali dan jumlah data tidak bertambah. Akun `admin` tidak diubah. Halaman dashboard, daftar buku, dan detail buku merespons HTTP 200 melalui Apache lokal.

### Jika login tidak berhasil

- Pastikan akun admin tersedia dan password yang dimasukkan sesuai password yang ditetapkan pada instalasi lokal. Password seed hanya untuk proses bootstrap lokal dan tidak boleh dipakai untuk deployment.
- Setelah lima kegagalan login, tunggu sampai masa pembatasan berakhir sebelum mencoba lagi.
- Pastikan `config/config.php` menunjuk ke database `mini_digital_library` pada host dan kredensial MySQL yang sama dengan phpMyAdmin.
- Di phpMyAdmin, pilih database itu dan pastikan tabel `users` memiliki baris `username=admin`, `role=admin`. Hash seed dimulai dengan `$2y$`.
- Jika tabel `users` kosong, impor SQL seed. **Perhatian:** file SQL instalasi membuat ulang tabel aplikasi yang disebut di dalamnya, sehingga mengimpor ulang akan menghapus data aplikasi yang sudah tersimpan.

# SISTEM MANAJEMEN AKADEMIK KAMPUS
## UniAdmin - Management System

### 🎓 Deskripsi
Sistem Manajemen Akademik Kampus yang komprehensif dengan fitur CRUD lengkap untuk mengelola data mahasiswa, fakultas, program studi, mata kuliah, dan laporan.

### ✨ Fitur Utama
1. **Dashboard Interaktif**
   - Statistik real-time
   - Grafik tren pendaftaran
   - Distribusi departemen
   - Aktivitas mahasiswa terbaru

2. **Manajemen Mahasiswa**
   - CRUD lengkap (Create, Read, Update, Delete)
   - Filter berdasarkan program studi dan status
   - Detail profil mahasiswa
   - Tracking IPK

3. **Manajemen Fakultas**
   - Manajemen data fakultas
   - Program studi per fakultas
   - Informasi dekan

4. **Manajemen Mata Kuliah**
   - CRUD mata kuliah
   - Pengaturan SKS dan semester
   - Status aktif/non-aktif

5. **Laporan & Analitik**
   - Statistik per program studi
   - Distribusi IPK
   - Laporan per tahun masuk
   - Export data (Excel, PDF, Print)

6. **Sistem Autentikasi**
   - Login admin yang aman
   - Session management
   - Password hashing

### 🛠️ Teknologi
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, Tailwind CSS, JavaScript (jQuery)
- **Icons**: Material Symbols
- **Fonts**: Google Fonts (Lexend)

### 📋 Persyaratan Sistem
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx)
- PDO Extension enabled

### 🚀 Instalasi

#### 1. Extract File
Extract file ZIP ke folder web server Anda (htdocs untuk XAMPP, www untuk WAMP)

#### 2. Import Database
- Buka phpMyAdmin
- Buat database baru dengan nama `akademik_kampus`
- Import file `database/akademik_kampus.sql`

#### 3. Konfigurasi Database
Buka file `config/database.php` dan sesuaikan:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'akademik_kampus');
```

#### 4. Akses Aplikasi
Buka browser dan akses:
```
http://localhost/akademik-kampus/
```

### 🔐 Akun Login Default

**Admin:**
- Email: `admin@university.edu`
- Password: `password123`

### 📁 Struktur Folder
```
akademik-kampus/
├── config/
│   └── database.php          # Konfigurasi database
├── includes/
│   ├── header.php            # Header template
│   ├── footer.php            # Footer template
│   └── session.php           # Session management
├── database/
│   └── akademik_kampus.sql   # Database schema & data
├── index.php                 # Dashboard
├── login.php                 # Halaman login
├── logout.php                # Logout handler
├── mahasiswa.php             # CRUD Mahasiswa
├── fakultas.php              # CRUD Fakultas
├── mata-kuliah.php           # CRUD Mata Kuliah
├── laporan.php               # Laporan & Analytics
└── README.md                 # Dokumentasi
```

### 🎯 Cara Penggunaan

#### Dashboard
Lihat ringkasan statistik, grafik tren, dan aktivitas terbaru

#### Manajemen Mahasiswa
1. Klik menu "Mahasiswa"
2. Gunakan tombol "Tambah Mahasiswa Baru" untuk menambah data
3. Klik icon "Edit" untuk mengubah data
4. Klik icon "Hapus" untuk menghapus data
5. Gunakan filter untuk mencari berdasarkan program studi atau status

#### Manajemen Fakultas
1. Klik menu "Fakultas"
2. Tambah, edit, atau hapus data fakultas
3. Lihat jumlah program studi per fakultas

#### Manajemen Mata Kuliah
1. Klik menu "Mata Kuliah"
2. Kelola mata kuliah dengan SKS dan semester
3. Atur status aktif/non-aktif

#### Laporan
1. Klik menu "Laporan"
2. Lihat statistik lengkap
3. Export data sesuai kebutuhan

### 🔒 Keamanan
- Password di-hash menggunakan password_hash PHP
- Prepared statements untuk mencegah SQL Injection
- Session management yang aman
- Input validation

### 📊 Database Schema
Sistem ini menggunakan 11 tabel utama:
1. `admin` - Data administrator
2. `fakultas` - Data fakultas
3. `program_studi` - Program studi
4. `mahasiswa` - Data mahasiswa
5. `dosen` - Data dosen
6. `mata_kuliah` - Mata kuliah
7. `kelas` - Kelas perkuliahan
8. `krs` - Kartu Rencana Studi
9. `presensi` - Data kehadiran
10. `nilai` - Data nilai mahasiswa
11. `pengumuman` - Pengumuman kampus

### 🎨 Tema
- **Light Mode**: Interface terang
- **Dark Mode**: Interface gelap (default)
- Responsive design untuk mobile, tablet, dan desktop

### 📝 Catatan Pengembangan
- Sistem ini dikembangkan dengan PHP native dan PDO
- UI menggunakan Tailwind CSS untuk styling modern
- Semua tombol dan fitur sudah berfungsi
- Database sudah terisi dengan data sample

### 🐛 Troubleshooting

**Database connection error:**
- Pastikan MySQL service berjalan
- Check kredensial database di config/database.php
- Pastikan database sudah di-import

**Login gagal:**
- Gunakan kredensial yang benar (lihat di atas)
- Clear browser cache dan cookies

**Page tidak tampil dengan benar:**
- Pastikan koneksi internet aktif (untuk CDN Tailwind)
- Clear browser cache
- Coba browser yang berbeda

### 📞 Support
Untuk bantuan lebih lanjut, hubungi administrator sistem.

### 📜 License
© 2026 UniAdmin. All Rights Reserved.

---
**Developed with ❤️ for Educational Purpose**

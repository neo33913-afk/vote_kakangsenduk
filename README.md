# 🏆 Kakang Senduk — Voting Platform

Aplikasi web pemilihan **Kakang dan Senduk** yang dirancang untuk mempermudah proses pemungutan suara secara digital. Aplikasi ini menyediakan sistem voting online, pengelolaan kontestan, manajemen periode voting, serta pemantauan hasil suara secara real-time.

## 📖 Tentang Proyek

Kakang Senduk merupakan aplikasi berbasis web yang dikembangkan untuk mendigitalisasi proses pemilihan Kakang dan Senduk.

Melalui aplikasi ini, pengguna dapat melihat daftar kontestan, memberikan suara, dan memantau hasil voting. Admin memiliki akses khusus untuk mengelola data kontestan, pemilih, periode voting, dan hasil pemilihan.

Aplikasi dibuat dengan memperhatikan kemudahan penggunaan, keamanan data, serta tampilan antarmuka yang sederhana dan responsif.

## ✨ Fitur Aplikasi

### 👤 Pengguna / Pemilih
- Registrasi dan login akun.
- Melihat daftar kontestan.
- Melihat detail informasi kontestan.
- Melakukan voting kategori Kakang dan Senduk.
- Melihat klasemen perolehan suara.
- Melihat hasil voting yang diperbarui otomatis.

### 🔐 Administrator
- Dashboard admin.
- Manajemen data kontestan (CRUD).
- Manajemen akun pemilih.
- Manajemen periode voting.
- Mengaktifkan dan menonaktifkan periode voting.
- Melihat hasil voting berdasarkan kategori.
- Mengunduh hasil voting dalam format CSV.
- Melihat statistik partisipasi pemilih.

### 📊 Sistem Klasemen
- Peringkat kontestan berdasarkan jumlah suara.
- Pemisahan kategori Kakang dan Senduk.
- Persentase perolehan suara.
- Pembaruan data otomatis setiap 10 detik.
- Penanda khusus untuk peringkat pertama.
- Klasemen dapat diakses secara publik.

## 🛠️ Teknologi

| Teknologi | Kegunaan |
|---|---|
| PHP | Backend aplikasi |
| MySQL / MariaDB | Database |
| HTML5 | Struktur halaman |
| CSS3 | Styling antarmuka |
| JavaScript | Interaksi dan pembaruan otomatis |
| Bootstrap 5 | Komponen antarmuka |
| PDO | Koneksi dan pengelolaan database |
| Fetch API | Pengambilan data klasemen |
| JSON | Pertukaran data API |
| XAMPP | Lingkungan pengembangan lokal |

## 📂 Struktur Proyek

```text
app-voting/
├── admin/
│   ├── dashboard.php
│   ├── kontestan.php
│   ├── periode.php
│   ├── pemilih.php
│   └── hasil-voting.php
│
├── api/
│   ├── klasemen.php
│   └── dashboard-stats.php
│
├── assets/
│   ├── css/
│   ├── js/
│   │   ├── pemilih-admin.js
│   │   └── klasemen.js
│   └── images/
│
├── config/
│   └── app.php
│
├── database/
│   └── db_voting_kakang_senduk.sql
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── admin-header.php
│   ├── admin-footer.php
│   ├── admin-check.php
│   └── klasemen.php
│
├── pages/
│   ├── voting.php
│   ├── klasemen.php
│   └── detail-kontestan.php
│
├── proses/
│   ├── periode-simpan.php
│   ├── periode-toggle.php
│   └── pemilih-hapus.php
│
└── index.php
```

*Struktur di atas menggambarkan bagian proyek yang telah dibahas dan dapat disesuaikan dengan file aktual.*

## ⚙️ Instalasi

### 1. Persiapan

Pastikan sudah menginstal:
- XAMPP
- PHP
- MySQL atau MariaDB
- Web Browser

### 2. Clone Repository

```bash
git clone https://github.com/username/app-voting.git
```

### 3. Pindahkan Folder

Pindahkan folder proyek ke:

```text
C:\xampp\htdocs\app-voting
```

### 4. Jalankan XAMPP

Aktifkan:
- Apache
- MySQL

### 5. Import Database

1. Buka phpMyAdmin.
2. Buat database sesuai konfigurasi aplikasi.
3. Import file `database/db_voting_kakang_senduk.sql`.

### 6. Konfigurasi Aplikasi

Sesuaikan konfigurasi pada:

```text
config/app.php
```

Pastikan pengaturan database dan URL aplikasi sudah benar.

### 7. Jalankan Aplikasi

Buka browser dan akses:

```text
http://localhost/app-voting
```

## 🔒 Keamanan Aplikasi

Beberapa mekanisme keamanan yang diterapkan:

- Password akun menggunakan hashing.
- Prepared Statement menggunakan PDO.
- Validasi hak akses administrator.
- CSRF Token untuk melindungi formulir.
- Validasi input pengguna.
- Pembatasan penghapusan akun pemilih yang sudah memberikan suara.
- Pembatasan satu periode voting aktif.
- Pengamanan ekspor CSV terhadap formula injection.

## 📡 RESTful API

Aplikasi menyediakan endpoint API untuk mengambil data klasemen voting.

| Endpoint | Method | Fungsi |
|---|---|---|
| `/api/klasemen.php?kategori=Kakang` | GET | Mengambil klasemen Kakang |
| `/api/klasemen.php?kategori=Senduk` | GET | Mengambil klasemen Senduk |

Format respons menggunakan JSON.

## 🚀 Pengembangan Selanjutnya

- [ ] Penyempurnaan halaman klasemen publik.
- [ ] Penyempurnaan statistik dashboard admin.
- [ ] Pengujian keamanan aplikasi.
- [ ] Optimalisasi performa database.
- [ ] Penyempurnaan tampilan responsif.
- [ ] Pengujian keseluruhan fitur voting.

## 👨‍💻 Developer

**Kakang Senduk Voting Platform**

Dikembangkan sebagai proyek aplikasi web untuk mendukung digitalisasi pemilihan Kakang dan Senduk.

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan pembelajaran dan pengembangan aplikasi. Hak penggunaan dan distribusi mengikuti ketentuan pemilik proyek.

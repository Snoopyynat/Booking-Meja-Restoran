# Product Requirement Document (PRD)
## Sistem Informasi Booking Meja Restoran (Web-Based)

---

## 1. Ringkasan Eksekutif
Aplikasi Web Booking Meja Restoran dirancang untuk memberikan pengalaman reservasi yang intuitif, cepat, dan elegan bagi pelanggan, sekaligus memberikan kemudahan Pengelolaan Data (CRUD) bagi pihak restoran. Sistem dikembangkan menggunakan PHP Native, database MySQL (XAMPP), dan antarmuka Bootstrap 5 yang disesuaikan dengan CSS Animated Gradient.

---

## 2. Tujuan & Sasaran
* **Kemudahan Pelanggan:** Memungkinkan pelanggan memilih meja, memasukkan data diri, menentukan tanggal/waktu, dan jumlah tamu secara langsung.
* **Manajemen CRUD:** Menyediakan panel pengelolaan meja, data pemesan, jadwal reservasi, dan kapasitas tamu.
* **Estetika Antarmuka:** Menampilkan latar belakang *animated color gradient* yang lembut, bebas dari ikon/logo, dengan kombinasi tipografi serif (Times New Roman) untuk judul elegan dan sans-serif (Roboto) untuk kejelasan teks.

---

## 3. Fitur & Spesifikasi Fungsional

### 3.1 Sisi Pelanggan (Public View)
1. **Halaman Utama & Formulir Reservasi:**
   * Tampilan visual meja restoran (Status: Tersedia / Terisi).
   * Formulir input: Nama Lengkap, Nomor Kontak, Tanggal & Waktu Reservasi, Jumlah Tamu, dan Pilihan Nomor Meja.
2. **Kupon / Konfirmasi Reservasi:**
   * Tanda terima digital setelah berhasil membuat pesanan.

### 3.2 Sisi Administrator (CRUD Management)
1. **Manage Layout & Nomor Meja (CRUD Meja):**
   * *Create:* Menambah meja baru (Nomor Meja, Kapasitas, Lokasi/Layout).
   * *Read:* Melihat daftar meja dan statusnya.
   * *Update:* Mengubah kapasitas atau nomor meja.
   * *Delete:* Menghapus meja dari sistem.
2. **Manage Reservasi (CRUD Booking):**
   * *Create:* Memasukkan booking manual dari admin.
   * *Read:* Melihat daftar pelanggan, waktu reservasi, dan detail meja.
   * *Update:* Mengubah status reservasi (Pending, Dikonfirmasi, Selesai, Batal).
   * *Delete:* Menghapus data reservasi.

---

## 4. Desain & Panduan Antarmuka (UI/UX)

* **Skema Warna:** CSS Animated Linear Gradient (transisi lembut antara warna ungu gelap, biru malam, dan magenta).
* **Ikonografi:** **TIDAK menggunakan ikon atau logo** sama sekali. Hanya mengandalkan ruang bersih, batas tipis, dan tipografi statis.
* **Tipografi:**
  * **Judul / Heading / Brand Text:** `Times New Roman`, `Times`, `serif` (Kesan Klasik & Elegan).
  * **Teks Bodi / Form Input / Tabel / Tombol:** `'Roboto'`, `sans-serif` (Kesan Modern & Keterbacaan Tinggi).

---

## 5. Struktur Database (SQL Schema)

Sistem menggunakan database bernama `db_restoran`.

```sql
CREATE DATABASE IF NOT EXISTS db_restoran;
USE db_restoran;

-- Tabel Meja Restoran
CREATE TABLE IF NOT EXISTS meja (
    id_meja INT AUTO_INCREMENT PRIMARY KEY,
    nomor_meja VARCHAR(10) NOT NULL UNIQUE,
    kapasitas INT NOT NULL,
    lokasi VARCHAR(50) DEFAULT 'Indoor',
    status ENUM('Tersedia', 'Terisi', 'Maintenance') DEFAULT 'Tersedia'
);

-- Tabel Pelanggan & Reservasi
CREATE TABLE IF NOT EXISTS reservasi (
    id_reservasi INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemesan VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    id_meja INT NOT NULL,
    waktu_reservasi DATETIME NOT NULL,
    jumlah_tamu INT NOT NULL,
    status_booking ENUM('Pending', 'Dikonfirmasi', 'Selesai', 'Batal') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_meja) REFERENCES meja(id_meja) ON DELETE CASCADE
);

-- Data Awal Meja
INSERT INTO meja (nomor_meja, kapasitas, lokasi, status) VALUES
('M-01', 2, 'Indoor', 'Tersedia'),
('M-02', 4, 'Indoor', 'Tersedia'),
('M-03', 4, 'Outdoor', 'Tersedia'),
('M-04', 6, 'VIP Room', 'Tersedia'),
('M-05', 8, 'VIP Room', 'Tersedia');
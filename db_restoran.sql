-- Database Schema untuk Sistem Informasi Booking Meja Restoran
CREATE DATABASE IF NOT EXISTS restorant;
USE restorant;

-- Tabel Meja Restoran
CREATE TABLE IF NOT EXISTS meja (
    id_meja INT AUTO_INCREMENT PRIMARY KEY,
    nomor_meja VARCHAR(10) NOT NULL UNIQUE,
    kapasitas INT NOT NULL,
    lokasi VARCHAR(50) DEFAULT 'Indoor',
    status ENUM('Tersedia', 'Terisi', 'Maintenance') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal Meja (Seed Data)
INSERT INTO meja (nomor_meja, kapasitas, lokasi, status) VALUES
('M-01', 2, 'Indoor', 'Tersedia'),
('M-02', 4, 'Indoor', 'Tersedia'),
('M-03', 4, 'Outdoor', 'Tersedia'),
('M-04', 6, 'VIP Room', 'Tersedia'),
('M-05', 8, 'VIP Room', 'Tersedia');

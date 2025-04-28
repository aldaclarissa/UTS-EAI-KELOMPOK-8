CREATE DATABASE IF NOT EXISTS db_inquiries;
USE db_inquiries;

CREATE TABLE IF NOT EXISTS pertanyaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    nama_nasabah VARCHAR(255) NOT NULL,
    no_telp VARCHAR(30) NOT NULL,
    alamat VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    pertanyaan TEXT NOT NULL,
    status ENUM('menunggu', 'diproses', 'selesai') DEFAULT 'menunggu',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    jawaban_admin TEXT NULL
);


CREATE DATABASE IF NOT EXISTS db_tickets;
USE db_tickets;

CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_nasabah INT NOT NULL,
    keluhan TEXT,
    status ENUM('terbuka', 'diproses', 'ditutup') DEFAULT 'terbuka',
    tanggapan TEXT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
); 
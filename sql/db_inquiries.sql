CREATE DATABASE IF NOT EXISTS db_inquiries;
USE db_inquiries;

CREATE TABLE IF NOT EXISTS pertanyaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    nama_customer VARCHAR(255) NOT NULL,
    no_telp VARCHAR(30) NOT NULL,
    alamat VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    pertanyaan TEXT NOT NULL,
    status ENUM('menunggu', 'diproses', 'selesai') DEFAULT 'menunggu',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    jawaban_admin TEXT NULL
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('nasabah','admin')
); 
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$sOpwkY7k1SYQo0t52C1OH.6YPp3bGgpMXojS.36tjEwuzHFougpGa', 'admin'),
('nasabah', '$2y$10$bN51E1hLZJeGV0Ybrjz.w.2peNAN7ZyF6cHfXj4vUBWrNKw9TWGD6', 'nasabah'); 
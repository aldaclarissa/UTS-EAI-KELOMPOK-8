CREATE DATABASE IF NOT EXISTS db_feedbacks;
USE db_feedbacks;

CREATE TABLE IF NOT EXISTS feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_nasabah INT NOT NULL,
    id_tiket INT NOT NULL,
    penilaian INT CHECK (penilaian >= 1 AND penilaian <= 5),
    komentar TEXT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
); 
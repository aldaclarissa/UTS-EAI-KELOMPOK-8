USE db_tickets;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('nasabah','admin')
);

INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$sOpwkY7k1SYQo0t52C1OH.6YPp3bGgpMXojS.36tjEwuzHFougpGa', 'admin'),
('nasabah', '$2y$10$bN51E1hLZJeGV0Ybrjz.w.2peNAN7ZyF6cHfXj4vUBWrNKw9TWGD6', 'nasabah'); 

--password : admin
--password : nasabah

# Bank Service SOA Impementation

# Overview
Project ini merupakan implementasi dari Servis-Oriented Architecture (SOA) untuk sistem layanan bank. Sistem ini terdiri dari beberapa microservices yang menangani berbagai aspek operasi bank, termasuk pertanyaan pelanggan, tiket dukungan, dan umpan balik pengguna.


# Struktur Projek
TugasUTSKel8/
├── config/
│   ├── db_feedbacks.php
│   ├── db_inquiries.php
│   └── db_tickets.php
├── services/
│   ├── customer_inquiries.php
│   ├── support_tickets.php
│   └── user_feedbacks.php
├── index.php
├── login.php
├── logout.php
└── session_check.php

## Services

### 1. Customer Inquiries Service (`customer_inquiries.php`)
- **Purpose**: Menangani pertanyaan dan konsultasi dari pelanggan
- **Operations**:
  - GET: Mengambil semua pertanyaan atau pertanyaan spesifik berdasarkan ID
  - POST: Membuat pertanyaan baru
  - PUT: Memperbarui
  - DELETE: Menghapus Pertanyaan

### 2. Support Tickets Service (`support_tickets.php`)
- **Purpose**: Mengelola tiket dukungan untuk permasalahan pelanggan 
- **Operations**:
  - GET: Melihat semua tiket atau tiket spesifik
  - POST: Membuat tiket dukungan baru
  - PUT: Memperbarui status dan informasi tiket
  - DELETE: Menghapus tiket

### 3. User Feedbacks Service (`user_feedbacks.php`)
- **Purpose**: Menangani umpan balik dan penilaian dari pelanggan
- **Operations**:
  - GET: Melihat semua umpan balik atau umpan balik spesifik
  - POST: Mengirimkan umpan balik baru
  - PUT: Memperbaiki umpan balik yang ada
  - DELETE: Menghapus umpan balik


## Database StructureStruktur basis data

### 1. Customer Inquiries Database (`db_inquiries`)
- Menyimpan pertanyaan dan konsultasi dari pelanggan
- Tables:
  - `pertanyaan`: Menyimoan oertanyaan
  - `users`: Menyimpan data authentikasi pengguna dan peran pengguna

### 2. Support Tickets Database (`db_tickets`)
- Mengelola data tiket dukungan pelanggan
- Tables:
  - `tickets`: menyimpan informasi tiket "dukungan/support"
  - `users`: Menyimoan data autentikasi pengguna dan peran pengguna

### 3. Feedback Database (`db_feedbacks`)
- Menyimpan data umpan balik pelanggan
- Tables:
  - `feedbacks`: Menyimpan data umpan balik dan penilaian pelanggan
  - `users`: MEnyimpan data autentikai pengguna dan peran pengguna

## Authentication Flow
1. pengguna login melalui `login.php`
2. Sesi (session) dibuat dan sisimpan
3. `session_check.php` memvalidasi sesi pengguna untuk setiap permintaan
4. pengguna logout melalui `logout.php`

## Service Flow

### Customer Inquiry Flow
1. Pelanggan mengirimkan pertanyaan melalui layanan pertanyaan 
2. Pertanyaan disimpan dalam basis data pertanyaan
3. Staff dukungan dapat melihat dan merespon pertanyaan

### Support Ticket Flow
1. Staf support/dukungan membua tiket berdasarkan pertanyaan pelanggan 
2. Pertanyaan disimpan dalam basis data pertanyaan
3. Staf dukungan dapat melihat dan merespon pertanyaan

### Feedback Flow
1. Pelanggan memberikan umpan balik setelah penyelesaian tiket
2. Umpan balik disimpan dalam basis data tiket
3. Administrator sistem dapat diperbarui selama penyelesaian masalah

## API Endpoints

### Customer Inquiries
- `GET /services/customer_inquiries.php` - Menampilkan semua pertanyaan
- `GET /services/customer_inquiries.php?id={id}` - Menampilkan pertanyaan spesifik
- `POST /services/customer_inquiries.php` - membuat pertanyaan baru
- `PUT /services/customer_inquiries.php` - memperbarui pertanyaan
- `DELETE /services/customer_inquiries.php` - Menghapus pertanyaan

### Support Tickets
- `GET /services/support_tickets.php` - Menampilkan semua tiket
- `GET /services/support_tickets.php?id={id}` - Menampilkan tiket spesifik
- `POST /services/support_tickets.php` - Membuat tiket baru
- `PUT /services/support_tickets.php` - Memperbarui tiket
- `DELETE /services/support_tickets.php` - Menghapus tiket

### User Feedbacks
- `GET /services/user_feedbacks.php` - Menampilkan semua umpan balik
- `GET /services/user_feedbacks.php?id={id}` - Menampilkan umpan balik spesifik
- `POST /services/user_feedbacks.php` - Menampilkan umpan balik spesifik
- `PUT /services/user_feedbacks.php` - Memperbarui feedback
- `DELETE /services/user_feedbacks.php` - menghapus feedback


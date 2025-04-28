<?php
require_once '../config/db_tickets.php';
require_once '../session_check.php';
require_once '../utils/HttpClient.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = DatabaseTickets::getConnection();

// Ambil daftar pertanyaan untuk dropdown nama customer
$inquiryList = [];
try {
    $inquiryDb = new PDO('mysql:host=localhost;dbname=db_inquiries;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    if ($_SESSION['role'] === 'admin') {
        $inquiryList = $inquiryDb->query('SELECT id, nama_customer, pertanyaan FROM pertanyaan ORDER BY id DESC')->fetchAll();
    } else {
        $stmt = $inquiryDb->prepare('SELECT id, nama_customer, pertanyaan FROM pertanyaan WHERE id_user = ? ORDER BY id DESC');
        $stmt->execute([$_SESSION['user_id']]);
        $inquiryList = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $inquiryList = [];
}

// Jika GET tanpa id, tampilkan UI HTML
if ($method === 'GET' && !isset($_GET['id'])) {
    // Jika ada id_pertanyaan di URL, tampilkan detail ticket terkait inquiry
    if (isset($_GET['id_pertanyaan'])) {
        $id_pertanyaan = intval($_GET['id_pertanyaan']);
        // Ambil inquiry
        $inquiryDb = new PDO('mysql:host=localhost;dbname=db_inquiries;charset=utf8mb4', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $stmtInq = $inquiryDb->prepare('SELECT * FROM pertanyaan WHERE id = ?');
        $stmtInq->execute([$id_pertanyaan]);
        $inquiry = $stmtInq->fetch();
        // Ambil ticket terkait inquiry
        $stmtTicket = $db->prepare('SELECT * FROM tickets WHERE id_pertanyaan = ? ORDER BY id DESC LIMIT 1');
        $stmtTicket->execute([$id_pertanyaan]);
        $ticket = $stmtTicket->fetch();
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Detail Ticket</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                body { background: #f4f7fb; color: #222; font-family: 'Poppins', Arial, sans-serif; margin: 0; }
                .navbar {
                    background-color: #003580 !important;
                    padding: 0.75rem 1rem;
                }
                .navbar-brand {
                    font-weight: 600;
                    font-size: 1.2rem;
                    color: #fff !important;
                }
                .navbar-text {
                    color: #fff !important;
                    margin-right: 16px;
                    font-weight: 500;
                    font-size: 0.95rem;
                }
                .btn-danger.btn-sm {
                    background-color: #e53935;
                    border: none;
                    padding: 6px 12px;
                    font-size: 0.9rem;
                    font-weight: 500;
                    border-radius: 6px;
                }
                .btn-danger.btn-sm:hover {
                    background-color: #c62828;
                }
                .detail-card {
                    background: #fff;
                    border-radius: 14px;
                    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
                    padding: 32px 28px 24px 28px;
                    max-width: 600px;
                    margin: 40px auto 0 auto;
                }
                h2 {
                    color: #003580;
                    font-weight: 700;
                    margin-bottom: 28px;
                    text-align: center;
                }
                .card-header {
                    background: #e3ebf6;
                    color: #003580;
                    font-weight: 600;
                    border-radius: 10px 10px 0 0;
                    padding: 12px 18px;
                    font-size: 1.1rem;
                }
                .card-body {
                    padding: 18px 18px 10px 18px;
                    text-align: left;
                }
                .card-body b {
                    color: #003580;
                    font-weight: 600;
                    min-width: 120px;
                    display: inline-block;
                }
                .btn-back {
                    background: #003580;
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    padding: 10px 22px;
                    font-weight: 600;
                    font-size: 1rem;
                    transition: background 0.2s;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
                    margin-top: 18px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                .btn-back:hover {
                    background: #0056b3;
                }
            </style>
        </head>
        <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="../index.html"><i class="fas fa-building-columns me-2"></i>Bank Service</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="customer_inquiries.php">Inquiries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="support_tickets.php">Tickets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="user_feedbacks.php">Feedback</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center ms-auto">
                        <span class="navbar-text text-white fw-semibold me-3">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
                        </span>
                        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="detail-card">
            <h2><i class="fas fa-ticket-alt me-2"></i>Detail Ticket</h2>
            <div class="card mb-3">
                <div class="card-header">Data Inquiry</div>
                <div class="card-body">
                    <b>Nama Nasabah:</b> <?= htmlspecialchars($inquiry['nama_customer'] ?? '-') ?><br>
                    <b>Pertanyaan:</b> <?= htmlspecialchars($inquiry['pertanyaan'] ?? '-') ?><br>
                    <b>Status Inquiry:</b> <?= htmlspecialchars($inquiry['status'] ?? '-') ?><br>
                </div>
            </div>
            <?php if ($ticket): ?>
            <div class="card mb-3">
                <div class="card-header">Data Ticket</div>
                <div class="card-body">
                    <b>ID Agen:</b> <?= htmlspecialchars($ticket['id_agen']) ?><br>
                    <b>Tanggapan:</b> <?= htmlspecialchars($ticket['tanggapan'] ?? '') ?><br>
                    <b>Status Ticket:</b> <?= htmlspecialchars($ticket['status']) ?><br>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">Daftar Tiket</div>
            <?php endif; ?>
            <a href="support_tickets.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Ticket</a>
        </div>
        </body>
        </html>
        <?php
        exit;
    }
    if ($_SESSION['role'] === 'admin') {
        $stmt = $db->query("SELECT * FROM tickets ORDER BY id DESC");
    } else {
        // Ambil id pertanyaan milik nasabah
        $inquiryIds = array_column($inquiryList, 'id');
        $inquiryIdsStr = implode(',', array_map('intval', $inquiryIds));
        $stmt = $db->query("SELECT * FROM tickets WHERE id_pertanyaan IN ($inquiryIdsStr) ORDER BY id DESC");
    }
    $rows = $stmt ? $stmt->fetchAll() : [];
    if (!$stmt) {
        echo "<div style='color:red'>Query error: " . implode(' ', $db->errorInfo()) . "</div>";
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Support Tickets</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
        <style>
            body { font-family: 'Poppins', Arial, sans-serif; margin: 0; background: #f4f7fb; color: #222; }
            h2 { color: #003580; font-weight: 700; margin-top: 32px; margin-bottom: 24px; text-align: center; }
            .navbar {
                background-color: #003580 !important;
                padding: 0.75rem 1rem;
            }
            .navbar-brand {
                font-weight: 600;
                font-size: 1.2rem;
                color: #fff !important;
            }
            .navbar-nav .nav-link {
                color: #fff !important;
                font-weight: 500;
                font-size: 1rem;
            }
            .navbar-nav .nav-link.active {
                color: #ffd600 !important;
            }
            .navbar-text {
                color: #fff !important;
                margin-right: 16px;
                font-weight: 500;
                font-size: 0.95rem;
            }
            .btn-danger.btn-sm {
                background-color: #e53935;
                border: none;
                padding: 6px 12px;
                font-size: 0.9rem;
                font-weight: 500;
                border-radius: 6px;
            }
            .btn-danger.btn-sm:hover {
                background-color: #c62828;
            }
            .ticket-card {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.07);
                padding: 32px 28px 24px 28px;
                max-width: 1100px;
                margin: 32px auto 0 auto;
            }
            form#addTicketForm {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                justify-content: center;
                margin-bottom: 18px;
            }
            form#addTicketForm input[type="text"],
            form#addTicketForm select {
                flex: 1 1 180px;
                padding: 10px 14px;
                border-radius: 8px;
                border: 1px solid #cfd8dc;
                background: #f8fafc;
                font-size: 1rem;
                transition: border 0.2s;
            }
            form#addTicketForm input[type="text"]:focus,
            form#addTicketForm select:focus {
                border: 1.5px solid #003580;
                outline: none;
            }
            form#addTicketForm button {
                background: #003580;
                color: #fff;
                border: none;
                border-radius: 8px;
                padding: 10px 22px;
                font-weight: 600;
                font-size: 1rem;
                transition: background 0.2s;
                box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            }
            form#addTicketForm button:hover {
                background: #0056b3;
            }
            table {
                width: 100%;
                margin: 0 auto 0 auto;
                border-collapse: separate;
                border-spacing: 0;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 12px rgba(0,0,0,0.06);
                overflow: hidden;
            }
            th, td {
                padding: 13px 10px;
                border-bottom: 1px solid #e3e8ee;
                text-align: center;
                font-size: 1rem;
            }
            th {
                background: #e3ebf6;
                color: #003580;
                font-weight: 600;
            }
            tr:last-child td {
                border-bottom: none;
            }
            tr:hover {
                background: #f0f6ff;
                transition: background 0.2s;
            }
            button.edit, button.delete {
                padding: 4px 10px;
                border-radius: 6px;
                border: none;
                font-weight: 500;
                font-size: 0.92rem;
                margin: 0 2px;
                cursor: pointer;
                transition: background 0.2s;
                height: 28px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                line-height: 1;
            }
            button.edit i, button.delete i { font-size: 1em; }
            button.edit { background: #03dac6; color: #003580; }
            button.edit:hover { background: #00bfae; color: #fff; }
            button.delete { background: #e53935; color: #fff; }
            button.delete:hover { background: #b71c1c; }
            td:last-child {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 6px;
                min-width: 120px;
            }
            @media (max-width: 900px) {
                .ticket-card { padding: 18px 4px; }
                table { font-size: 0.97rem; }
                form#addTicketForm { flex-direction: column; gap: 8px; }
            }
            #ticketModal {
                display: none;
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0,0,0,0.7);
                z-index: 9999;
                align-items: center;
                justify-content: center;
            }
            #ticketForm {
                background: #fff;
                padding: 28px 28px 16px 28px;
                border-radius: 12px;
                max-width: 420px;
                margin: auto;
                color: #222;
                box-shadow: 0 4px 24px rgba(0,0,0,0.13);
                font-family: 'Poppins', Arial, sans-serif;
            }
            #ticketForm input, #ticketForm select {
                width: 100%;
                margin-bottom: 12px;
                padding: 10px 12px;
                border-radius: 7px;
                border: 1px solid #cfd8dc;
                background: #f8fafc;
                font-size: 1rem;
            }
            #ticketForm input:focus, #ticketForm select:focus {
                border: 1.5px solid #003580;
                outline: none;
            }
            #ticketForm button[type="submit"] {
                background: #03dac6;
                color: #003580;
                padding: 8px 20px;
                border-radius: 7px;
                font-weight: 600;
                border: none;
                margin-right: 8px;
                transition: background 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            #ticketForm button[type="submit"]:hover {
                background: #00bfae;
                color: #fff;
            }
            #cancelTicketBtn {
                background: #e53935;
                color: #fff;
                padding: 8px 20px;
                border-radius: 7px;
                font-weight: 600;
                border: none;
                transition: background 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            #cancelTicketBtn:hover {
                background: #b71c1c;
            }
            #ticketForm h4 {
                color: #003580;
                font-weight: 700;
                margin-bottom: 18px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="../index.html"><i class="fas fa-building-columns me-2"></i>Bank Service</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="customer_inquiries.php">Inquiries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="support_tickets.php">Tickets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="user_feedbacks.php">Feedback</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center ms-auto">
                        <span class="navbar-text text-white fw-semibold me-3">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
                        </span>
                        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="ticket-card">
            <h2><i class="fas fa-ticket-alt me-2"></i>Support Tickets</h2>
            <!-- Inline Add Ticket Form -->
            <form id="addTicketForm" style="display:flex;gap:8px;align-items:center;margin-bottom:16px;">
                <select name="id_pertanyaan" id="add_id_pertanyaan" required style="min-width:160px;">
                    <option value="">Pilih Inquiry</option>
                    <?php foreach($inquiryList as $inq): ?>
                        <option value="<?= $inq['id'] ?>" data-nama="<?= htmlspecialchars($inq['nama_customer']) ?>" data-pertanyaan="<?= htmlspecialchars($inq['pertanyaan'] ?? '') ?>">
                            <?= htmlspecialchars($inq['nama_customer']) ?> - <?= htmlspecialchars($inq['pertanyaan'] ?? '') ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <input type="text" name="nama_nasabah" id="add_nama_nasabah" placeholder="Nama Nasabah" readonly style="min-width:160px;">
                <input type="text" name="keluhan" id="add_keluhan" placeholder="Keluhan" style="min-width:180px;" required>
                <input type="text" name="tanggapan" id="add_tanggapan" placeholder="Tanggapan" style="min-width:180px;">
                <select name="status" id="add_status" required style="min-width:120px;">
                    <option value="terbuka">terbuka</option>
                    <option value="diproses">diproses</option>
                    <option value="ditutup">ditutup</option>
                </select>
                <button type="submit" style="background:#00358">Tambah Ticket</button>
            </form>
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Nasabah</th>
                    <th>Keluhan</th>
                    <th>Tanggapan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <tbody id="ticketTable">
                <?php $no=1; foreach($rows as $row): ?>
                    <?php
                        $nama_nasabah = '';
                        foreach ($inquiryList as $inq) {
                            if ($inq['id'] == $row['id_pertanyaan']) {
                                $nama_nasabah = $inq['nama_customer'];
                                break;
                            }
                        }
                        // Keluhan diambil dari field keluhan pada tabel tickets, bukan dari pertanyaan inquiry
                        $keluhan = $row['keluhan'] ?? '';
                    ?>
                    <tr data-id="<?= $row['id'] ?>" data-id_pertanyaan="<?= $row['id_pertanyaan'] ?>">
                        <td><?= $no++ ?></td>
                        <td class="nama_nasabah"><?= htmlspecialchars($nama_nasabah) ?></td>
                        <td class="keluhan"><?= htmlspecialchars($keluhan) ?></td>
                        <td class="tanggapan"><?= htmlspecialchars($row['tanggapan'] ?? '') ?></td>
                        <td class="status"><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <button class="edit"><i class="fas fa-pen"></i> Edit</button>
                            <button class="delete"><i class="fas fa-trash"></i> Hapus</button>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <!-- Modal Tambah/Edit Ticket -->
            <div id="ticketModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;">
                <form id="ticketForm">
                    <h4 id="modalTitle"><i class="fas fa-pen me-2"></i>Edit Ticket</h4>
                    <input type="hidden" name="id" id="ticket_id">
                    <div>
                        <label for="ticket_id_pertanyaan" style="font-weight:500;font-size:0.98em;">Nama Nasabah</label>
                        <select name="id_pertanyaan" id="ticket_id_pertanyaan" required>
                            <option value="">Pilih Nasabah</option>
                            <?php foreach($inquiryList as $inq): ?>
                                <option value="<?= $inq['id'] ?>"><?= htmlspecialchars($inq['nama_customer']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div>
                        <label for="ticket_keluhan" style="font-weight:500;font-size:0.98em;">Keluhan</label>
                        <input type="text" name="keluhan" id="ticket_keluhan" placeholder="Keluhan" required>
                    </div>
                    <div>
                        <label for="ticket_tanggapan" style="font-weight:500;font-size:0.98em;">Tanggapan</label>
                        <input type="text" name="tanggapan" id="ticket_tanggapan" placeholder="Tanggapan" required>
                    </div>
                    <div>
                        <label for="ticket_status" style="font-weight:500;font-size:0.98em;">Status</label>
                        <select name="status" id="ticket_status" required>
                            <option value="terbuka">terbuka</option>
                            <option value="diproses">diproses</option>
                            <option value="ditutup">ditutup</option>
                        </select>
                    </div>
                    <button type="submit"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" id="cancelTicketBtn"><i class="fas fa-times"></i> Batal</button>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        // Inline add ticket
        fetch('../get_user_info.php').then(r=>r.json()).then(data=>{
            if(data.role==='nasabah'){
                document.getElementById('add_tanggapan').style.display='none';
                document.getElementById('add_status').style.display='none';
                document.getElementById('add_keluhan').style.display='block';
                // Otomatis isi nama customer dari inquiry yang dipilih
                document.getElementById('add_id_pertanyaan').onchange = function() {
                    const selected = this.options[this.selectedIndex];
                    document.getElementById('add_nama_nasabah').value = selected.getAttribute('data-nama') || '';
                };
                // Set nama customer pertama kali
                document.getElementById('add_id_pertanyaan').dispatchEvent(new Event('change'));
            } else {
                document.getElementById('add_nama_nasabah').style.display='none';
                document.getElementById('add_keluhan').style.display='none';
            }
        });
        document.getElementById('addTicketForm').onsubmit = async function(e) {
            e.preventDefault();
            const id_pertanyaan = document.getElementById('add_id_pertanyaan').value;
            const keluhan = document.getElementById('add_keluhan').value;
            if (!id_pertanyaan || !keluhan) {
                alert('Inquiry dan keluhan harus diisi!');
                return;
            }
            let body = {id_pertanyaan, keluhan};
            let isAdmin = false;
            await fetch('../get_user_info.php').then(r=>r.json()).then(data=>{
                if(data.role==='admin'){
                    isAdmin = true;
                    body.status = document.getElementById('add_status').value;
                    body.tanggapan = document.getElementById('add_tanggapan').value;
                } else {
                    body.status = 'terbuka';
                }
            });
            if (!isAdmin) delete body.tanggapan;
            const res = await fetch('support_tickets.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify(body)
            });
            const result = await res.json();
            if (!result.success) {
                alert('Gagal menambah ticket!\n' + (result.error || ''));
                return;
            }
            location.reload();
        };
        // Show modal tambah (DISABLED)
        document.getElementById('showAddModal')?.remove();
        // Edit
        document.querySelectorAll('.edit').forEach(btn => {
            btn.onclick = function() {
                const tr = this.closest('tr');
                document.getElementById('modalTitle').innerText = 'Edit Ticket';
                document.getElementById('ticket_id').value = tr.dataset.id;
                document.getElementById('ticket_id_pertanyaan').value = tr.dataset.id_pertanyaan;
                document.getElementById('ticket_keluhan').value = tr.querySelector('.keluhan').textContent;
                document.getElementById('ticket_tanggapan').value = tr.querySelector('.tanggapan').textContent;
                document.getElementById('ticket_status').value = tr.querySelector('.status').textContent;
                document.getElementById('ticketModal').style.display = 'flex';
            }
        });
        // Hapus
        document.querySelectorAll('.delete').forEach(btn => {
            btn.onclick = async function() {
                if(confirm('Yakin hapus?')) {
                    const id = this.closest('tr').dataset.id;
                    await fetch('support_tickets.php', {
                        method: 'DELETE',
                        headers: {'Content-Type':'application/json'},
                        body: JSON.stringify({id})
                    });
                    location.reload();
                }
            }
        });
        // Submit form tambah/edit
        document.getElementById('ticketForm').onsubmit = async function(e) {
            e.preventDefault();
            const id = document.getElementById('ticket_id').value;
            const id_pertanyaan = document.getElementById('ticket_id_pertanyaan').value;
            const keluhan = document.getElementById('ticket_keluhan').value;
            const tanggapan = document.getElementById('ticket_tanggapan').value;
            const status = document.getElementById('ticket_status').value;
            const method = id ? 'PUT' : 'POST';
            const body = id ? {id, id_pertanyaan, keluhan, tanggapan, status} : {id_pertanyaan, keluhan, tanggapan, status};
            await fetch('support_tickets.php', {
                method,
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify(body)
            });
            document.getElementById('ticketModal').style.display = 'none';
            location.reload();
        };
        fetch('../get_user_info.php').then(r=>r.json()).then(data=>{
            if(data.role==='nasabah'){
                document.getElementById('ticket_tanggapan').style.display='none';
            } else {
                document.getElementById('ticket_tanggapan').style.display='block';
            }
        });
        document.getElementById('cancelTicketBtn').onclick = function() {
            document.getElementById('ticketModal').style.display = 'none';
        };
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Hanya set header JSON untuk response API
header('Content-Type: application/json');

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT * FROM tickets WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $result = $stmt->fetch();
            echo json_encode($result);
        } else {
            if ($_SESSION['role'] === 'admin') {
                $stmt = $db->query("SELECT * FROM tickets ORDER BY id DESC");
                $rows = $stmt ? $stmt->fetchAll() : [];
            } else {
                // Ambil id pertanyaan milik nasabah
                $inquiryDb = new PDO('mysql:host=localhost;dbname=db_inquiries;charset=utf8mb4', 'root', '', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                $stmtInq = $inquiryDb->prepare('SELECT id FROM pertanyaan WHERE id_user = ?');
                $stmtInq->execute([$_SESSION['user_id']]);
                $inquiryIds = $stmtInq->fetchAll(PDO::FETCH_COLUMN);
                if (count($inquiryIds) > 0) {
                    $inquiryIdsStr = implode(',', array_map('intval', $inquiryIds));
                    $stmt = $db->query("SELECT * FROM tickets WHERE id_pertanyaan IN ($inquiryIdsStr) ORDER BY id DESC");
                    $rows = $stmt ? $stmt->fetchAll() : [];
                } else {
                    $rows = [];
                }
            }
            echo json_encode($rows);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $keluhan = $data['keluhan'] ?? null;
        $tanggapan = $data['tanggapan'] ?? null;
        $status = $data['status'] ?? 'terbuka';
        $id_user = $_SESSION['user_id'];
        try {
            $stmt = $db->prepare("INSERT INTO tickets (id_pertanyaan, keluhan, status, tanggapan, id_user) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['id_pertanyaan'],
                $keluhan,
                $status,
                $tanggapan,
                $id_user
            ]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE tickets SET id_pertanyaan=?, keluhan=?, tanggapan=?, status=?, id_user=? WHERE id=?");
        $stmt->execute([
            $data['id_pertanyaan'],
            $data['keluhan'] ?? null,
            $data['tanggapan'],
            $data['status'],
            $_SESSION['user_id'],
            $data['id']
        ]);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("DELETE FROM tickets WHERE id=?");
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
} 
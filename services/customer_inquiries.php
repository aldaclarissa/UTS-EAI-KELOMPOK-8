<?php
require_once '../config/db_inquiries.php';
require_once '../session_check.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = DatabaseInquiries::getConnection();

if ($method === 'GET' && !isset($_GET['id'])) {
    if ($_SESSION['role'] === 'admin') {
        $stmt = $db->query("SELECT * FROM pertanyaan ORDER BY id DESC");
    } else {
        $stmt = $db->prepare("SELECT * FROM pertanyaan WHERE id_user = ? ORDER BY id DESC");
        $stmt->execute([$_SESSION['user_id']]);
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
        <title>Daftar Inquiries</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
        <style>
            body { font-family: 'Poppins', Arial, sans-serif; margin: 0; background: #f4f7fb; color: #222; }
            h2 { color: #003580; font-weight: 700; margin-top: 32px; margin-bottom: 24px; text-align: center; }
            .inquiry-card {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.07);
                padding: 32px 28px 24px 28px;
                max-width: 1100px;
                margin: 32px auto 0 auto;
                overflow-x: auto;
            }
            .inquiry-card table {
                width: 100%;
                max-width: 100%;
                margin: 0 auto;
                box-sizing: border-box;
                border-radius: 12px;
                overflow: hidden;
                background: #fff;
            }
            form#addForm {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                justify-content: center;
                margin-bottom: 18px;
            }
            form#addForm input[type="text"] {
                flex: 1 1 180px;
                padding: 10px 14px;
                border-radius: 8px;
                border: 1px solid #cfd8dc;
                background: #f8fafc;
                font-size: 1rem;
                transition: border 0.2s;
            }
            form#addForm input[type="text"]:focus {
                border: 1.5px solid #003580;
                outline: none;
            }
            form#addForm button {
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
            form#addForm button:hover {
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
            .btn-info.btn-sm {
                background: #1976d2;
                color: #fff;
                border-radius: 6px;
                padding: 4px 10px;
                font-size: 0.92rem;
                font-weight: 500;
                border: none;
                transition: background 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                height: 28px;
                line-height: 1;
            }
            .btn-info.btn-sm i { font-size: 1em; }
            .btn-info.btn-sm:hover { background: #003580; }
            #editModal {
                display: none;
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0,0,0,0.7);
                z-index: 9999;
                align-items: center;
                justify-content: center;
            }
            #editForm {
                background: #fff;
                padding: 28px 28px 16px 28px;
                border-radius: 12px;
                max-width: 420px;
                margin: auto;
                color: #222;
                box-shadow: 0 4px 24px rgba(0,0,0,0.13);
            }
            #editForm input, #editForm select {
                width: 100%;
                margin-bottom: 12px;
                padding: 10px 12px;
                border-radius: 7px;
                border: 1px solid #cfd8dc;
                background: #f8fafc;
                font-size: 1rem;
            }
            #editForm input:focus, #editForm select:focus {
                border: 1.5px solid #003580;
                outline: none;
            }
            #editForm button[type="submit"] {
                background: #03dac6;
                color: #003580;
                padding: 8px 20px;
                border-radius: 7px;
                font-weight: 600;
                border: none;
                margin-right: 8px;
                transition: background 0.2s;
            }
            #editForm button[type="submit"]:hover {
                background: #00bfae;
                color: #fff;
            }
            #cancelEditBtn {
                background: #e53935;
                color: #fff;
                padding: 8px 20px;
                border-radius: 7px;
                font-weight: 600;
                border: none;
                transition: background 0.2s;
            }
            #cancelEditBtn:hover {
                background: #b71c1c;
            }
            @media (max-width: 900px) {
                .inquiry-card { padding: 18px 4px; }
                table { font-size: 0.97rem; }
                form#addForm { flex-direction: column; gap: 8px; }
            }
            /* Tombol aksi rata tengah dan sejajar */
            td:last-child {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 6px;
                min-width: 120px;
            }
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
            form#addForm select,
            #editForm select {
                flex: 1 1 180px;
                padding: 10px 14px;
                border-radius: 8px;
                border: 1px solid #cfd8dc;
                background: #f8fafc;
                font-size: 1rem;
                transition: border 0.2s;
                margin-bottom: 0;
            }
            form#addForm select:focus,
            #editForm select:focus {
                border: 1.5px solid #003580;
                outline: none;
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
                            <a class="nav-link active" href="customer_inquiries.php">Inquiries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="support_tickets.php">Tickets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="user_feedbacks.php">Feedback</a>
                        </li>
                    </ul>
                    <span class="navbar-text text-white fw-semibold me-3">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
                    </span>
                    <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
                </div>
            </div>
        </nav>
        <div class="inquiry-card">
            <h2><i class="fas fa-comments me-2"></i>Daftar Inquiries</h2>
            <form id="addForm">
                <input type="text" name="nama_nasabah" placeholder="Nama Nasabah" required>
                <input type="text" name="no_telp" placeholder="No Telp" required>
                <input type="text" name="alamat" placeholder="Alamat" required>
                <select name="subject" id="add_subject" required>
                    <option value="">Pilih Subjek</option>
                    <option value="Informasi Saldo Rekening">Informasi Saldo Rekening</option>
                    <option value="Cara Menutup Rekening Bank">Cara Menutup Rekening Bank</option>
                    <option value="Cara Pengajuan Kartu Kredit">Cara Pengajuan Kartu Kredit</option>
                    <option value="Perubahan Alamat atau Data Pribadi">Perubahan Alamat atau Data Pribadi</option>
                    <option value="Masa Berlaku Kartu ATM">Masa Berlaku Kartu ATM</option>
                    <option value="Syarat dan Ketentuan Kartu Kredit">Syarat dan Ketentuan Kartu Kredit</option>
                    <option value="Cara Mengganti PIN ATM">Cara Mengganti PIN ATM</option>
                    <option value="Cara Mengaktifkan Mobile Banking">Cara Mengaktifkan Mobile Banking</option>
                    <option value="Tata Cara Aktivasi SMS Banking">Tata Cara Aktivasi SMS Banking</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <input type="text" name="pertanyaan" placeholder="Pertanyaan" required>
                <button type="submit"><i class="fas fa-plus"></i> Tambah Inquiry</button>
            </form>
            <div class="table-responsive">
                <table>
                    <tr>
                        <th>No</th>
                        <th>Nama Nasabah</th>
                        <th>No Telp</th>
                        <th>Alamat</th>
                        <th>Subject</th>
                        <th>Pertanyaan</th>
                        <th>Status</th>
                        <th class="waktu-col" style="display:none;">Dibuat Pada</th>
                        <th class="waktu-col" style="display:none;">Diperbarui Pada</th>
                        <th>Jawaban Admin</th>
                        <th>Aksi</th>
                    </tr>
                    <tbody id="inquiryTable">
                    <?php $no=1; foreach($rows as $row): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= $no++ ?></td>
                            <td class="nama_nasabah"><?= htmlspecialchars(isset($row['nama_nasabah']) ? $row['nama_nasabah'] : (isset($row['nama_customer']) ? $row['nama_customer'] : '')) ?></td>
                            <td class="no_telp"><?= htmlspecialchars($row['no_telp']) ?></td>
                            <td class="alamat"><?= htmlspecialchars($row['alamat']) ?></td>
                            <td class="subject"><?= htmlspecialchars($row['subject']) ?></td>
                            <td class="pertanyaan"><?= htmlspecialchars($row['pertanyaan']) ?></td>
                            <td class="status"><?= htmlspecialchars($row['status']) ?></td>
                            <td class="dibuat_pada waktu-col" style="display:none;"><?= htmlspecialchars($row['dibuat_pada']) ?></td>
                            <td class="diperbarui_pada waktu-col" style="display:none;"><?= htmlspecialchars($row['diperbarui_pada']) ?></td>
                            <td class="jawaban_admin">
                            <?php
                            if ($_SESSION['role'] === 'nasabah' && $row['status'] === 'diproses') {
                                echo '<a href="support_tickets.php?id_pertanyaan=' . $row['id'] . '" class="btn btn-info btn-sm"><i class="fas fa-ticket-alt"></i> Lihat Ticket</a>';
                            } else {
                                echo htmlspecialchars($row['jawaban_admin'] ?? '');
                            }
                            ?>
                            </td>
                            <td>
                                <button class="edit"><i class="fas fa-pen"></i> Edit</button>
                                <button class="delete"><i class="fas fa-trash"></i> Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;">
                <form id="editForm" style="background:#fff;padding:28px 28px 16px 28px;border-radius:12px;max-width:420px;margin:auto;color:#222;box-shadow:0 4px 24px rgba(0,0,0,0.13);">
                    <h4 style="color:#003580;"><i class="fas fa-pen me-2"></i>Edit Inquiry</h4>
                    <input type="hidden" name="id" id="edit_id">
                    <div>
                        <label for="edit_nama_nasabah" style="font-weight:500;font-size:0.98em;">Nama Nasabah</label>
                        <input type="text" name="nama_nasabah" id="edit_nama_nasabah" placeholder="Nama Nasabah" required>
                    </div>
                    <div>
                        <label for="edit_no_telp" style="font-weight:500;font-size:0.98em;">No Telp</label>
                        <input type="text" name="no_telp" id="edit_no_telp" placeholder="No Telp" required>
                    </div>
                    <div>
                        <label for="edit_alamat" style="font-weight:500;font-size:0.98em;">Alamat</label>
                        <input type="text" name="alamat" id="edit_alamat" placeholder="Alamat" required>
                    </div>
                    <div>
                        <label for="edit_subject" style="font-weight:500;font-size:0.98em;">Subject</label>
                        <select name="subject" id="edit_subject" required>
                            <option value="">Pilih Subjek</option>
                            <option value="Informasi Saldo Rekening">Informasi Saldo Rekening</option>
                            <option value="Cara Menutup Rekening Bank">Cara Menutup Rekening Bank</option>
                            <option value="Cara Pengajuan Kartu Kredit">Cara Pengajuan Kartu Kredit</option>
                            <option value="Perubahan Alamat atau Data Pribadi">Perubahan Alamat atau Data Pribadi</option>
                            <option value="Masa Berlaku Kartu ATM">Masa Berlaku Kartu ATM</option>
                            <option value="Syarat dan Ketentuan Kartu Kredit">Syarat dan Ketentuan Kartu Kredit</option>
                            <option value="Cara Mengganti PIN ATM">Cara Mengganti PIN ATM</option>
                            <option value="Cara Mengaktifkan Mobile Banking">Cara Mengaktifkan Mobile Banking</option>
                            <option value="Tata Cara Aktivasi SMS Banking">Tata Cara Aktivasi SMS Banking</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_pertanyaan" style="font-weight:500;font-size:0.98em;">Pertanyaan</label>
                        <input type="text" name="pertanyaan" id="edit_pertanyaan" placeholder="Pertanyaan" required>
                    </div>
                    <div>
                        <label for="edit_status" style="font-weight:500;font-size:0.98em;">Status</label>
                        <select name="status" id="edit_status" required>
                            <option value="menunggu">menunggu</option>
                            <option value="diproses">diproses</option>
                            <option value="selesai">selesai</option>
                        </select>
                    </div>
                    <div id="admin_jawaban" style="display:none;">
                        <label for="edit_jawaban_admin" style="font-weight:500;font-size:0.98em;">Jawaban Admin</label>
                        <input type="text" name="jawaban_admin" id="edit_jawaban_admin" placeholder="Jawaban Admin">
                    </div>
                    <button type="submit"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" id="cancelEditBtn"><i class="fas fa-times"></i> Batal</button>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.getElementById('addForm').onsubmit = async function(e) {
            e.preventDefault();
            const nama_nasabah = this.nama_nasabah.value;
            const no_telp = this.no_telp.value;
            const alamat = this.alamat.value;
            const subject = this.subject.value;
            const pertanyaan = this.pertanyaan.value;
            await fetch('customer_inquiries.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({nama_nasabah, no_telp, alamat, subject, pertanyaan})
            });
            location.reload();
        };
        document.querySelectorAll('.edit').forEach(btn => {
            btn.onclick = function() {
                const tr = this.closest('tr');
                document.getElementById('edit_id').value = tr.dataset.id;
                document.getElementById('edit_nama_nasabah').value = tr.querySelector('.nama_nasabah').textContent;
                document.getElementById('edit_no_telp').value = tr.querySelector('.no_telp').textContent;
                document.getElementById('edit_alamat').value = tr.querySelector('.alamat').textContent;
                document.getElementById('edit_subject').value = tr.querySelector('.subject').textContent;
                document.getElementById('edit_pertanyaan').value = tr.querySelector('.pertanyaan').textContent;
                document.getElementById('edit_status').value = tr.querySelector('.status').textContent;
                fetch('../get_user_info.php').then(r=>r.json()).then(data=>{
                    if(data.role==='admin'){
                        document.getElementById('edit_status').parentElement.style.display='block';
                        document.getElementById('admin_jawaban').style.display='block';
                        document.getElementById('edit_jawaban_admin').value = tr.querySelector('.jawaban_admin').textContent;
                        document.getElementById('edit_jawaban_admin').disabled = false;
                    }else{
                        document.getElementById('edit_status').parentElement.style.display='none';
                        document.getElementById('admin_jawaban').style.display='none';
                        document.getElementById('edit_jawaban_admin').value = '';
                        document.getElementById('edit_jawaban_admin').disabled = true;
                    }
                });
                document.getElementById('editModal').style.display = 'flex';
            }
        });
        document.getElementById('cancelEditBtn').onclick = function() {
            document.getElementById('editModal').style.display = 'none';
        };
        document.getElementById('editForm').onsubmit = async function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_id').value;
            const nama_nasabah = document.getElementById('edit_nama_nasabah').value;
            const no_telp = document.getElementById('edit_no_telp').value;
            const alamat = document.getElementById('edit_alamat').value;
            const subject = document.getElementById('edit_subject').value;
            const pertanyaan = document.getElementById('edit_pertanyaan').value;
            const status = document.getElementById('edit_status').value;
            const jawaban_admin = document.getElementById('edit_jawaban_admin').value;
            let body = {id, nama_nasabah, no_telp, alamat, subject, pertanyaan, status};
            // Hanya admin yang boleh mengirim jawaban_admin
            await fetch('../get_user_info.php').then(r=>r.json()).then(data=>{
                if(data.role==='admin' && jawaban_admin) body.jawaban_admin = jawaban_admin;
            });
            await fetch('customer_inquiries.php', {
                method: 'PUT',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify(body)
            });
            document.getElementById('editModal').style.display = 'none';
            location.reload();
        };
        document.querySelectorAll('.delete').forEach(btn => {
            btn.onclick = async function() {
                if(confirm('Yakin hapus?')) {
                    const id = this.closest('tr').dataset.id;
                    await fetch('customer_inquiries.php', {
                        method: 'DELETE',
                        headers: {'Content-Type':'application/json'},
                        body: JSON.stringify({id})
                    });
                    location.reload();
                }
            }
        });
        fetch('../get_user_info.php').then(r=>r.json()).then(data=>{
            if(data.role==='admin'){
                document.querySelectorAll('.waktu-col').forEach(el=>el.style.display='table-cell');
            }else{
                document.querySelectorAll('.waktu-col').forEach(el=>el.style.display='none');
            }
        });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// API JSON Handling
header('Content-Type: application/json');

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT * FROM pertanyaan WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $result = $stmt->fetch();
            echo json_encode($result);
        } else {
            $stmt = $db->query("SELECT * FROM pertanyaan ORDER BY id DESC");
            $rows = $stmt ? $stmt->fetchAll() : [];
            echo json_encode($rows);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO pertanyaan (id_user, nama_nasabah, no_telp, alamat, subject, pertanyaan, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $data['nama_nasabah'],
            $data['no_telp'],
            $data['alamat'],
            $data['subject'],
            $data['pertanyaan'],
            'menunggu'
        ]);
        echo json_encode(['success' => true]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID harus disertakan']);
            exit;
        }
        $updateFields = [];
        $params = [];
        if (isset($data['nama_nasabah'])) {
            $updateFields[] = "nama_nasabah = ?";
            $params[] = $data['nama_nasabah'];
        }
        if (isset($data['no_telp'])) {
            $updateFields[] = "no_telp = ?";
            $params[] = $data['no_telp'];
        }
        if (isset($data['alamat'])) {
            $updateFields[] = "alamat = ?";
            $params[] = $data['alamat'];
        }
        if (isset($data['subject'])) {
            $updateFields[] = "subject = ?";
            $params[] = $data['subject'];
        }
        if (isset($data['pertanyaan'])) {
            $updateFields[] = "pertanyaan = ?";
            $params[] = $data['pertanyaan'];
        }
        // Hanya admin yang boleh update status dan jawaban_admin
        if ($_SESSION['role'] === 'admin') {
            $validStatus = ['menunggu', 'diproses', 'selesai'];
            // Jika admin mengisi jawaban_admin, status otomatis selesai
            if (isset($data['jawaban_admin']) && strlen(trim($data['jawaban_admin'])) > 0) {
                $updateFields[] = "jawaban_admin = ?";
                $params[] = $data['jawaban_admin'];
                $updateFields[] = "status = ?";
                $params[] = 'selesai';
            } else if (isset($data['status']) && in_array($data['status'], $validStatus)) {
                $updateFields[] = "status = ?";
                $params[] = $data['status'];
            }
        }
        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['error' => 'Tidak ada data yang akan diupdate']);
            exit;
        }
        $params[] = $data['id'];
        $query = "UPDATE pertanyaan SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        if (isset($data['status']) && $data['status'] === 'diproses' && $_SESSION['role'] === 'admin') {
            echo json_encode(['success' => true, 'redirect' => 'support_tickets.php']);
        } else {
            echo json_encode(['success' => true]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        // Hanya admin atau pemilik inquiry yang boleh hapus
        $stmt = $db->prepare("SELECT id_user FROM pertanyaan WHERE id = ?");
        $stmt->execute([$data['id']]);
        $row = $stmt->fetch();
        if ($_SESSION['role'] !== 'admin' && (!$row || $row['id_user'] != $_SESSION['user_id'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        $stmt = $db->prepare("DELETE FROM pertanyaan WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>

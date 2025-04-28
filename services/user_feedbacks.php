<?php
require_once '../config/db_feedbacks.php';
require_once '../session_check.php';

// Ambil daftar customer dari db_inquiries
$inquiryList = [];
try {
    $inquiryDb = new PDO('mysql:host=localhost;dbname=db_inquiries;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $inquiryList = $inquiryDb->query('SELECT id, nama_customer FROM pertanyaan ORDER BY id DESC')->fetchAll();
} catch (Exception $e) {
    $inquiryList = [];
}

$method = $_SERVER['REQUEST_METHOD'];
$db = DatabaseFeedbacks::getConnection();

// Jika GET tanpa id, tampilkan UI HTML
if ($method === 'GET' && !isset($_GET['id'])) {
    if ($_SESSION['role'] === 'admin') {
        $stmt = $db->query("SELECT * FROM feedbacks ORDER BY id DESC");
    } else {
        $stmt = $db->prepare("SELECT * FROM feedbacks WHERE id_user = ? ORDER BY id DESC");
        $stmt->execute([$_SESSION['user_id']]);
    }
    $rows = $stmt ? $stmt->fetchAll() : [];
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Feedback</title>
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
            .feedback-card {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.07);
                padding: 32px 28px 24px 28px;
                max-width: 1100px;
                margin: 32px auto 0 auto;
            }
            form#addFeedbackForm {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                justify-content: center;
                margin-bottom: 18px;
            }
            form#addFeedbackForm input[type="text"],
            form#addFeedbackForm input[type="number"],
            form#addFeedbackForm select {
                flex: 1 1 140px;
                padding: 10px 14px;
                border-radius: 8px;
                border: 1px solid #cfd8dc;
                background: #f8fafc;
                font-size: 1rem;
                transition: border 0.2s;
            }
            form#addFeedbackForm input[type="text"]:focus,
            form#addFeedbackForm input[type="number"]:focus,
            form#addFeedbackForm select:focus {
                border: 1.5px solid #003580;
                outline: none;
            }
            form#addFeedbackForm button {
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
            form#addFeedbackForm button:hover {
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
            .star { color: gold; font-size: 1.2em; }
            @media (max-width: 900px) {
                .feedback-card { padding: 18px 4px; }
                table { font-size: 0.97rem; }
                form#addFeedbackForm { flex-direction: column; gap: 8px; }
            }
            /* Modal Edit Feedback */
            #feedbackModal {
                display: none;
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0,0,0,0.7);
                z-index: 9999;
                align-items: center;
                justify-content: center;
            }
            #feedbackForm {
                background: #fff;
                padding: 28px 28px 16px 28px;
                border-radius: 12px;
                max-width: 420px;
                margin: auto;
                color: #222;
                box-shadow: 0 4px 24px rgba(0,0,0,0.13);
                font-family: 'Poppins', Arial, sans-serif;
            }
            #feedbackForm input, #feedbackForm select {
                width: 100%;
                margin-bottom: 12px;
                padding: 10px 12px;
                border-radius: 7px;
                border: 1px solid #cfd8dc;
                background: #f8fafc;
                font-size: 1rem;
            }
            #feedbackForm input:focus, #feedbackForm select:focus {
                border: 1.5px solid #003580;
                outline: none;
            }
            #feedbackForm button[type="submit"] {
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
            #feedbackForm button[type="submit"]:hover {
                background: #00bfae;
                color: #fff;
            }
            #cancelFeedbackBtn {
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
            #cancelFeedbackBtn:hover {
                background: #b71c1c;
            }
            #feedbackForm h4 {
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
                            <a class="nav-link" href="support_tickets.php">Tickets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="user_feedbacks.php">Feedback</a>
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
        <div class="feedback-card">
            <h2><i class="fas fa-star me-2"></i>Daftar Feedback</h2>
            <!-- Inline Add Feedback Form -->
            <form id="addFeedbackForm" style="display:flex;gap:8px;align-items:center;margin-bottom:16px;">
                <select name="id_pelanggan" id="add_id_pelanggan" required style="min-width:160px;">
                    <option value="">Nama Nasabah</option>
                    <?php foreach($inquiryList as $inq): ?>
                        <option value="<?= $inq['id'] ?>"><?= htmlspecialchars($inq['nama_customer']) ?></option>
                    <?php endforeach ?>
                </select>
                <input type="number" name="id_tiket" id="add_id_tiket" placeholder="ID Tiket" required style="min-width:120px;">
                <span id="add_rating" style="min-width:120px;"></span>
                <input type="hidden" name="penilaian" id="add_penilaian" required>
                <input type="text" name="komentar" id="add_komentar" placeholder="Komentar" required style="min-width:180px;">
                <button type="submit" style="#003580">Tambah Feedback</button>
            </form>
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama Nasabah</th>
                    <th>Keluhan Nasabah</th>
                    <th>Penilaian</th>
                    <th>Komentar</th>
                    <th>Aksi</th>
                </tr>
                <tbody id="feedbackTable">
                <?php $no=1; foreach($rows as $row): ?>
                    <?php
                        $nama_customer = '';
                        $keluhan = '';
                        foreach ($inquiryList as $inq) {
                            if ($inq['id'] == $row['id_pelanggan']) {
                                $nama_customer = $inq['nama_customer'];
                                break;
                            }
                        }
                        // Ambil keluhan dari tabel tickets
                        $keluhan = '';
                        try {
                            $ticketDb = new PDO('mysql:host=localhost;dbname=db_tickets;charset=utf8mb4', 'root', '', [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                            ]);
                            $stmtKeluhan = $ticketDb->prepare('SELECT keluhan FROM tickets WHERE id = ?');
                            $stmtKeluhan->execute([$row['id_tiket']]);
                            $keluhan = $stmtKeluhan->fetchColumn();
                        } catch (Exception $e) {}
                    ?>
                    <tr data-id="<?= $row['id'] ?>" data-id_pelanggan="<?= $row['id_pelanggan'] ?>" data-id_tiket="<?= $row['id_tiket'] ?>" data-penilaian="<?= $row['penilaian'] ?>" data-komentar="<?= htmlspecialchars($row['komentar']) ?>">
                        <td><?= $no++ ?></td>
                        <td class="nama_customer"><?= htmlspecialchars($nama_customer) ?></td>
                        <td class="keluhan_pelanggan"><?= htmlspecialchars($keluhan ?? '') ?></td>
                        <td class="penilaian"><?php for($i=1;$i<=5;$i++) echo $i<=$row['penilaian']?'<span class="star">★</span>':'<span class="star" style="color:#444;">★</span>'; ?></td>
                        <td class="komentar"><?= htmlspecialchars($row['komentar']) ?></td>
                        <td>
                            <button class="edit"><i class="fas fa-pen"></i> Edit</button>
                            <button class="delete"><i class="fas fa-trash"></i> Hapus</button>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
            <!-- Modal Edit Feedback -->
            <div id="feedbackModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;">
                <form id="feedbackForm">
                    <h4 id="modalTitle"><i class="fas fa-pen me-2"></i>Edit Feedback</h4>
                    <input type="hidden" name="id" id="feedback_id">
                    <div>
                        <label for="feedback_id_pelanggan" style="font-weight:500;font-size:0.98em;">Nama Nasabah</label>
                        <select name="id_pelanggan" id="feedback_id_pelanggan" required style="width:100%;margin-bottom:8px;">
                            <option value="">Pilih Nasabah</option>
                            <?php foreach($inquiryList as $inq): ?>
                                <option value="<?= $inq['id'] ?>"><?= htmlspecialchars($inq['nama_customer']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div>
                        <label for="feedback_id_tiket" style="font-weight:500;font-size:0.98em;">ID Tiket</label>
                        <input type="number" name="id_tiket" id="feedback_id_tiket" placeholder="ID Tiket" required style="width:100%;margin-bottom:8px;">
                    </div>
                    <div>
                        <label for="feedback_penilaian" style="font-weight:500;font-size:0.98em;">Penilaian</label>
                        <span id="edit_rating"></span>
                        <input type="hidden" name="penilaian" id="feedback_penilaian" required>
                    </div>
                    <div>
                        <label for="feedback_komentar" style="font-weight:500;font-size:0.98em;">Komentar</label>
                        <input type="text" name="komentar" id="feedback_komentar" placeholder="Komentar" required style="width:100%;margin-bottom:8px;">
                    </div>
                    <button type="submit"><i class="fas fa-save"></i> Simpan</button>
                    <button type="button" id="cancelFeedbackBtn"><i class="fas fa-times"></i> Batal</button>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        // Star rating for add
        function renderStars(container, inputId, value=0) {
            container.innerHTML = '';
            for(let i=1;i<=5;i++) {
                const star = document.createElement('span');
                star.className = 'star';
                star.textContent = '★';
                star.style.cursor = 'pointer';
                star.style.color = i<=value ? 'gold' : '#444';
                star.onclick = () => {
                    document.getElementById(inputId).value = i;
                    renderStars(container, inputId, i);
                };
                container.appendChild(star);
            }
        }
        renderStars(document.getElementById('add_rating'), 'add_penilaian', 0);
        document.getElementById('add_penilaian').value = 0;

        // Inline add feedback
        document.getElementById('addFeedbackForm').onsubmit = async function(e) {
            e.preventDefault();
            const id_pelanggan = document.getElementById('add_id_pelanggan').value;
            const id_tiket = document.getElementById('add_id_tiket').value;
            const penilaian = document.getElementById('add_penilaian').value;
            const komentar = document.getElementById('add_komentar').value;
            await fetch('user_feedbacks.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({id_pelanggan, id_tiket, penilaian, komentar})
            });
            location.reload();
        };
        // Edit
        document.querySelectorAll('.edit').forEach(btn => {
            btn.onclick = function() {
                const tr = this.closest('tr');
                document.getElementById('modalTitle').innerText = 'Edit Feedback';
                document.getElementById('feedback_id').value = tr.dataset.id;
                document.getElementById('feedback_id_pelanggan').value = tr.dataset.id_pelanggan;
                document.getElementById('feedback_id_tiket').value = tr.dataset.id_tiket;
                document.getElementById('feedback_komentar').value = tr.dataset.komentar;
                document.getElementById('feedback_penilaian').value = tr.dataset.penilaian;
                renderStars(document.getElementById('edit_rating'), 'feedback_penilaian', parseInt(tr.dataset.penilaian));
                document.getElementById('feedbackModal').style.display = 'flex';
            }
        });
        // Hide modal
        document.getElementById('cancelFeedbackBtn').onclick = function() {
            document.getElementById('feedbackModal').style.display = 'none';
        };
        // Submit edit
        document.getElementById('feedbackForm').onsubmit = async function(e) {
            e.preventDefault();
            const id = document.getElementById('feedback_id').value;
            const id_pelanggan = document.getElementById('feedback_id_pelanggan').value;
            const id_tiket = document.getElementById('feedback_id_tiket').value;
            const penilaian = document.getElementById('feedback_penilaian').value;
            const komentar = document.getElementById('feedback_komentar').value;
            await fetch('user_feedbacks.php', {
                method: 'PUT',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({id, id_pelanggan, id_tiket, penilaian, komentar})
            });
            document.getElementById('feedbackModal').style.display = 'none';
            location.reload();
        };
        // Hapus
        document.querySelectorAll('.delete').forEach(btn => {
            btn.onclick = async function() {
                if(confirm('Yakin hapus?')) {
                    const id = this.closest('tr').dataset.id;
                    await fetch('user_feedbacks.php', {
                        method: 'DELETE',
                        headers: {'Content-Type':'application/json'},
                        body: JSON.stringify({id})
                    });
                    location.reload();
                }
            }
        });
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
            $stmt = $db->prepare("SELECT * FROM feedbacks WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $result = $stmt->fetch();
        } else {
            $stmt = $db->query("SELECT * FROM feedbacks ORDER BY id DESC");
            $rows = $stmt ? $stmt->fetchAll() : [];
            if (!$stmt) {
                echo "<div style='color:red'>Query error: " . implode(' ', $db->errorInfo()) . "</div>";
            }
        }
        echo json_encode($result);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("INSERT INTO feedbacks (id_pelanggan, id_tiket, penilaian, komentar, id_user) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['id_pelanggan'], $data['id_tiket'], $data['penilaian'], $data['komentar'], $_SESSION['user_id']]);
        echo json_encode(['success' => true]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("UPDATE feedbacks SET id_pelanggan=?, id_tiket=?, penilaian=?, komentar=? WHERE id=?");
        $stmt->execute([$data['id_pelanggan'], $data['id_tiket'], $data['penilaian'], $data['komentar'], $data['id']]);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("DELETE FROM feedbacks WHERE id=?");
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
} 
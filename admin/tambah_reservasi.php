<?php
require_once __DIR__ . '/../config/kuis_db.php';

$errorMsg = '';

// Fetch semua meja untuk dropdown
$stmtMeja = $pdo->query("SELECT * FROM meja ORDER BY nomor_meja ASC");
$daftarMeja = $stmtMeja->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPemesan    = isset($_POST['nama_pemesan']) ? trim(htmlspecialchars($_POST['nama_pemesan'])) : '';
    $noHp           = isset($_POST['no_hp']) ? trim(htmlspecialchars($_POST['no_hp'])) : '';
    $email          = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
    $idMeja         = isset($_POST['id_meja']) ? filter_var($_POST['id_meja'], FILTER_VALIDATE_INT) : 0;
    $waktuInput     = isset($_POST['waktu_reservasi']) ? trim($_POST['waktu_reservasi']) : '';
    $jumlahTamu     = isset($_POST['jumlah_tamu']) ? filter_var($_POST['jumlah_tamu'], FILTER_VALIDATE_INT) : 0;
    $statusBooking  = isset($_POST['status_booking']) ? trim($_POST['status_booking']) : 'Dikonfirmasi';

    if (empty($namaPemesan) || empty($noHp) || !$idMeja || empty($waktuInput) || !$jumlahTamu) {
        $errorMsg = 'Mohon isi semua kolom yang wajib.';
    } else {
        $waktuReservasi = date('Y-m-d H:i:s', strtotime($waktuInput));

        // Cek kapasitas meja
        $stmtCekMeja = $pdo->prepare("SELECT * FROM meja WHERE id_meja = :id");
        $stmtCekMeja->execute([':id' => $idMeja]);
        $meja = $stmtCekMeja->fetch();

        if (!$meja) {
            $errorMsg = 'Meja tidak ditemukan.';
        } elseif ($jumlahTamu > (int)$meja['kapasitas']) {
            $errorMsg = "Jumlah tamu ({$jumlahTamu}) melebihi kapasitas meja {$meja['nomor_meja']} ({$meja['kapasitas']} tamu).";
        } else {
            // Cek bentrok
            $stmtBentrok = $pdo->prepare("
                SELECT COUNT(*) FROM reservasi 
                WHERE id_meja = :id_meja 
                  AND status_booking IN ('Pending', 'Dikonfirmasi') 
                  AND ABS(TIMESTAMPDIFF(MINUTE, waktu_reservasi, :waktu_reservasi)) < 120
            ");
            $stmtBentrok->execute([
                ':id_meja' => $idMeja,
                ':waktu_reservasi' => $waktuReservasi
            ]);
            $isBentrok = $stmtBentrok->fetchColumn();

            if ($isBentrok > 0) {
                $errorMsg = "Peringatan: Meja {$meja['nomor_meja']} telah dipesan pada jam yang berdekatan (+/- 2 jam).";
            } else {
                // Insert dengan Prepared Statement
                $stmtInsert = $pdo->prepare("
                    INSERT INTO reservasi (nama_pemesan, no_hp, email, id_meja, waktu_reservasi, jumlah_tamu, status_booking) 
                    VALUES (:nama, :hp, :email, :meja, :waktu, :tamu, :status)
                ");
                $stmtInsert->execute([
                    ':nama' => $namaPemesan,
                    ':hp'   => $noHp,
                    ':email'=> $email,
                    ':meja' => $idMeja,
                    ':waktu'=> $waktuReservasi,
                    ':tamu' => $jumlahTamu,
                    ':status'=> $statusBooking
                ]);

                // Update status meja di tabel meja
                $mejaStatus = in_array($statusBooking, ['Pending', 'Dikonfirmasi']) ? 'Terisi' : 'Tersedia';
                $stmtUpdateMeja = $pdo->prepare("UPDATE meja SET status = :status WHERE id_meja = :id_meja");
                $stmtUpdateMeja->execute([':status' => $mejaStatus, ':id_meja' => $idMeja]);

                header('Location: index.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Booking Manual - Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand" href="index.php">Maido di Lima <span class="badge bg-secondary font-monospace" style="font-size: 0.75rem;">ADMIN</span></a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-custom btn-sm">Kembali ke Daftar Reservasi</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <?php if (!empty($errorMsg)): ?>
                    <div class="alert alert-danger glass-card border-danger text-white mb-4 alert-dismissible fade show" role="alert">
                        <strong>Gagal Menyimpan:</strong> <?= htmlspecialchars($errorMsg) ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="glass-card">
                    <div class="glass-card-header">
                        <h1 class="h4 mb-0 fw-bold">Input Reservasi Manual oleh Admin</h1>
                    </div>
                    <div class="glass-card-body">
                        <form action="tambah_reservasi.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nama_pemesan" class="form-label">Nama Pemesan *</label>
                                    <input type="text" class="form-control" id="nama_pemesan" name="nama_pemesan" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="no_hp" class="form-label">Nomor HP / WhatsApp *</label>
                                    <input type="tel" class="form-control" id="no_hp" name="no_hp" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email (Opsional)</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="waktu_reservasi" class="form-label">Tanggal & Waktu Reservasi *</label>
                                    <input type="datetime-local" class="form-control" id="waktu_reservasi" name="waktu_reservasi" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="jumlah_tamu" class="form-label">Jumlah Tamu *</label>
                                    <input type="number" class="form-control" id="jumlah_tamu" name="jumlah_tamu" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="id_meja" class="form-label">Pilih Meja *</label>
                                    <select class="form-select" id="id_meja" name="id_meja" required>
                                        <option value="" selected disabled>-- Pilih Meja --</option>
                                        <?php foreach ($daftarMeja as $m): ?>
                                            <option value="<?= htmlspecialchars($m['id_meja']) ?>">
                                                Meja <?= htmlspecialchars($m['nomor_meja']) ?> - <?= htmlspecialchars($m['lokasi']) ?> (Maks. <?= htmlspecialchars($m['kapasitas']) ?> Tamu) - Status: <?= htmlspecialchars($m['status']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="status_booking" class="form-label">Status Initial *</label>
                                    <select class="form-select" id="status_booking" name="status_booking" required>
                                        <option value="Pending">Pending</option>
                                        <option value="Dikonfirmasi" selected>Dikonfirmasi</option>
                                        <option value="Selesai">Selesai</option>
                                        <option value="Batal">Batal</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="my-4 border-secondary">

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="index.php" class="btn btn-outline-custom">Batal</a>
                                <button type="submit" class="btn btn-primary-custom px-4">Simpan Booking</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

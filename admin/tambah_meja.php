<?php
require_once __DIR__ . '/../config/kuis_db.php';

$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomorMeja = isset($_POST['nomor_meja']) ? trim(htmlspecialchars($_POST['nomor_meja'])) : '';
    $kapasitas = isset($_POST['kapasitas']) ? filter_var($_POST['kapasitas'], FILTER_VALIDATE_INT) : 0;
    $lokasi    = isset($_POST['lokasi']) ? trim(htmlspecialchars($_POST['lokasi'])) : 'Indoor';
    $status    = isset($_POST['status']) ? trim($_POST['status']) : 'Tersedia';

    $allowedStatus = ['Tersedia', 'Terisi', 'Maintenance'];

    if (empty($nomorMeja) || !$kapasitas || !in_array($status, $allowedStatus)) {
        $errorMsg = 'Mohon isi semua kolom data meja dengan benar.';
    } else {
        // Cek duplikasi nomor meja
        $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM meja WHERE nomor_meja = :nomor");
        $stmtCek->execute([':nomor' => $nomorMeja]);
        if ($stmtCek->fetchColumn() > 0) {
            $errorMsg = "Nomor meja '{$nomorMeja}' sudah digunakan. Silakan gunakan nomor lain.";
        } else {
            try {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO meja (nomor_meja, kapasitas, lokasi, status) 
                    VALUES (:nomor, :kapasitas, :lokasi, :status)
                ");
                $stmtInsert->execute([
                    ':nomor'    => $nomorMeja,
                    ':kapasitas'=> $kapasitas,
                    ':lokasi'   => $lokasi,
                    ':status'   => $status
                ]);

                header('Location: meja.php?msg=' . urlencode("Meja '{$nomorMeja}' berhasil ditambahkan."));
                exit;
            } catch (PDOException $e) {
                $errorMsg = 'Gagal menyimpan meja ke database.';
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
    <title>Tambah Meja Baru - Admin</title>
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
                <a href="meja.php" class="btn btn-outline-custom btn-sm">Kembali ke Daftar Meja</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                
                <?php if (!empty($errorMsg)): ?>
                    <div class="alert alert-danger glass-card border-danger text-white mb-4 alert-dismissible fade show" role="alert">
                        <strong>Gagal Menyimpan:</strong> <?= htmlspecialchars($errorMsg) ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="glass-card">
                    <div class="glass-card-header">
                        <h1 class="h4 mb-0 fw-bold">Tambah Data Meja Baru</h1>
                    </div>
                    <div class="glass-card-body">
                        <form action="tambah_meja.php" method="POST">
                            <div class="mb-3">
                                <label for="nomor_meja" class="form-label">Nomor Meja *</label>
                                <input type="text" class="form-control" id="nomor_meja" name="nomor_meja" placeholder="Contoh: M-06" required>
                            </div>
                            <div class="mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas Tamu (Orang) *</label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="1" max="50" placeholder="Contoh: 4" required>
                            </div>
                            <div class="mb-3">
                                <label for="lokasi" class="form-label">Lokasi / Area *</label>
                                <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Contoh: Indoor / Outdoor / VIP Room" required>
                            </div>
                            <div class="mb-4">
                                <label for="status" class="form-label">Status Awal Meja *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Tersedia" selected>Tersedia</option>
                                    <option value="Terisi">Terisi</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="meja.php" class="btn btn-outline-custom">Batal</a>
                                <button type="submit" class="btn btn-primary-custom px-4">Simpan Meja</button>
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

<?php
require_once __DIR__ . '/../config/kuis_db.php';

$idMeja = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;

if (!$idMeja) {
    header('Location: meja.php');
    exit;
}

// Fetch data meja saat ini
$stmt = $pdo->prepare("SELECT * FROM meja WHERE id_meja = :id");
$stmt->execute([':id' => $idMeja]);
$meja = $stmt->fetch();

if (!$meja) {
    header('Location: meja.php?err=' . urlencode('Data meja tidak ditemukan.'));
    exit;
}

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
        // Cek jika nomor meja diubah dan mengalami duplikasi dengan meja lain
        $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM meja WHERE nomor_meja = :nomor AND id_meja != :id");
        $stmtCek->execute([':nomor' => $nomorMeja, ':id' => $idMeja]);
        if ($stmtCek->fetchColumn() > 0) {
            $errorMsg = "Nomor meja '{$nomorMeja}' sudah digunakan oleh meja lain.";
        } else {
            try {
                $stmtUpdate = $pdo->prepare("
                    UPDATE meja 
                    SET nomor_meja = :nomor, kapasitas = :kapasitas, lokasi = :lokasi, status = :status 
                    WHERE id_meja = :id
                ");
                $stmtUpdate->execute([
                    ':nomor'    => $nomorMeja,
                    ':kapasitas'=> $kapasitas,
                    ':lokasi'   => $lokasi,
                    ':status'   => $status,
                    ':id'       => $idMeja
                ]);

                header('Location: meja.php?msg=' . urlencode("Data Meja '{$nomorMeja}' berhasil diperbarui."));
                exit;
            } catch (PDOException $e) {
                $errorMsg = 'Gagal memperbarui data meja.';
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
    <title>Edit Meja - Admin</title>
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
                        <h1 class="h4 mb-0 fw-bold">Edit Data Meja: <?= htmlspecialchars($meja['nomor_meja']) ?></h1>
                    </div>
                    <div class="glass-card-body">
                        <form action="edit_meja.php?id=<?= htmlspecialchars($idMeja) ?>" method="POST">
                            <div class="mb-3">
                                <label for="nomor_meja" class="form-label">Nomor Meja *</label>
                                <input type="text" class="form-control" id="nomor_meja" name="nomor_meja" value="<?= htmlspecialchars($meja['nomor_meja']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="kapasitas" class="form-label">Kapasitas Tamu (Orang) *</label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="1" max="50" value="<?= htmlspecialchars($meja['kapasitas']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="lokasi" class="form-label">Lokasi / Area *</label>
                                <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?= htmlspecialchars($meja['lokasi']) ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="status" class="form-label">Status Meja *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Tersedia" <?= $meja['status'] === 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                    <option value="Terisi" <?= $meja['status'] === 'Terisi' ? 'selected' : '' ?>>Terisi</option>
                                    <option value="Maintenance" <?= $meja['status'] === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="meja.php" class="btn btn-outline-custom">Batal</a>
                                <button type="submit" class="btn btn-primary-custom px-4">Simpan Perubahan</button>
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

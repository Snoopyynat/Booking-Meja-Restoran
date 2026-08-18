<?php
require_once __DIR__ . '/../config/kuis_db.php';

$successMsg = isset($_GET['msg']) ? trim($_GET['msg']) : '';
$errorMsg = isset($_GET['err']) ? trim($_GET['err']) : '';

// Fetch semua meja dari database
$stmt = $pdo->query("SELECT * FROM meja ORDER BY nomor_meja ASC");
$daftarMeja = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Meja Restoran - Admin</title>
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
            <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Kelola Reservasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="meja.php">Kelola Meja</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a href="../index.php" class="btn btn-outline-custom btn-sm">Lihat Website Publik</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom border-secondary">
            <div>
                <h1 class="h2 fw-bold mb-1">Manajemen Layout & Nomor Meja</h1>
                <p class="text-muted-custom mb-0">Tambah, ubah, atau hapus data meja restoran dan lokasinya.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="tambah_meja.php" class="btn btn-primary-custom">
                    + Tambah Meja Baru
                </a>
            </div>
        </div>

        <!-- Alert Notification -->
        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success glass-card border-success text-white mb-4 alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($successMsg) ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger glass-card border-danger text-white mb-4 alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($errorMsg) ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tabel Daftar Meja -->
        <div class="glass-card">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nomor Meja</th>
                            <th>Kapasitas</th>
                            <th>Lokasi / Area</th>
                            <th>Status Ketersediaan</th>
                            <th class="text-center">Aksi (CRUD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($daftarMeja) > 0): ?>
                            <?php $no = 1; foreach ($daftarMeja as $m): ?>
                                <?php
                                    $badgeClass = 'badge-tersedia';
                                    if ($m['status'] === 'Terisi') $badgeClass = 'badge-terisi';
                                    elseif ($m['status'] === 'Maintenance') $badgeClass = 'badge-maintenance';
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong class="text-white h5 mb-0"><?= htmlspecialchars($m['nomor_meja']) ?></strong></td>
                                    <td><?= htmlspecialchars($m['kapasitas']) ?> Tamu</td>
                                    <td><?= htmlspecialchars($m['lokasi']) ?></td>
                                    <td>
                                        <span class="badge-status <?= $badgeClass ?>">
                                            <?= htmlspecialchars($m['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="edit_meja.php?id=<?= htmlspecialchars($m['id_meja']) ?>" class="btn btn-outline-warning btn-sm me-1">Edit</a>
                                        <form action="hapus_meja.php" method="POST" class="d-inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Meja <?= htmlspecialchars($m['nomor_meja']) ?>? Semua data reservasi terkait juga akan terhapus!');">
                                            <input type="hidden" name="id_meja" value="<?= htmlspecialchars($m['id_meja']) ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted-custom">
                                    Belum ada data meja di dalam database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once __DIR__ . '/config/kuis_db.php';

$idReservasi = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;

if (!$idReservasi) {
    header('Location: index.php');
    exit;
}

// Fetch detail reservasi beserta informasi meja
$stmt = $pdo->prepare("
    SELECT r.*, m.nomor_meja, m.lokasi, m.kapasitas 
    FROM reservasi r
    JOIN meja m ON r.id_meja = m.id_meja
    WHERE r.id_reservasi = :id
");
$stmt->execute([':id' => $idReservasi]);
$data = $stmt->fetch();

if (!$data) {
    header('Location: index.php');
    exit;
}

// Format kode booking unik
$kodeBooking = 'RSV-' . str_pad($data['id_reservasi'], 5, '0', STR_PAD_LEFT);

// Format tanggal & waktu
$waktuFormatted = date('d F Y, H:i', strtotime($data['waktu_reservasi'])) . ' WIB';
$createdAtFormatted = date('d F Y, H:i', strtotime($data['created_at'])) . ' WIB';

// Kelas badge status
$statusClass = 'badge-pending';
if ($data['status_booking'] === 'Dikonfirmasi') {
    $statusClass = 'badge-dikonfirmasi';
} elseif ($data['status_booking'] === 'Selesai') {
    $statusClass = 'badge-selesai';
} elseif ($data['status_booking'] === 'Batal') {
    $statusClass = 'badge-batal';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Reservasi - <?= htmlspecialchars($kodeBooking) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom no-print py-3">
        <div class="container">
            <a class="navbar-brand" href="index.php">Maido di Lima</a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-custom btn-sm">Buat Reservasi Baru</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <!-- Alert Sukses (Hanya Tampil di Layar) -->
                <div class="alert alert-success glass-card border-success text-white mb-4 no-print text-center"
                    role="alert">
                    <h2 class="h5 mb-1 fw-bold">Reservasi Berhasil Dibuat!</h2>
                    <p class="mb-0" style="font-size: 0.9rem;">
                        Tanda terima digital Anda telah berhasil diterbitkan. Silakan simpan atau cetak bukti ini.
                    </p>
                </div>

                <!-- Receipt Box -->
                <div class="receipt-container shadow-lg">
                    <div class="text-center border-bottom border-secondary pb-4 mb-4">
                        <h1 class="h2 fw-bold mb-1">Tanda Terima Reservasi Digital</h1>
                        <p class="text-muted-custom mb-2">Restoran Maido di Lima</p>
                        <div class="font-monospace text-light h5 mb-0">Kode Booking:
                            <strong><?= htmlspecialchars($kodeBooking) ?></strong>
                        </div>
                    </div>

                    <div class="row g-3" style="font-size: 1rem;">
                        <div class="col-6">
                            <span class="text-muted-custom d-block">Nama Pemesan</span>
                            <span class="customer-name-tag"><?= htmlspecialchars($data['nama_pemesan']) ?></span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted-custom d-block">Status Reservasi</span>
                            <span class="badge-status <?= $statusClass ?>">
                                <?= htmlspecialchars($data['status_booking']) ?>
                            </span>
                        </div>

                        <div class="col-6">
                            <span class="text-muted-custom d-block">Nomor HP / Whatsapp</span>
                            <span class="text-white"><?= htmlspecialchars($data['no_hp']) ?></span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted-custom d-block">Email</span>
                            <span
                                class="text-white"><?= !empty($data['email']) ? htmlspecialchars($data['email']) : '-' ?></span>
                        </div>

                        <div class="col-12">
                            <hr class="my-2 border-secondary opacity-50">
                        </div>

                        <div class="col-6">
                            <span class="text-muted-custom d-block">Nomor Meja</span>
                            <strong class="text-white h5 mb-0"><?= htmlspecialchars($data['nomor_meja']) ?></strong>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted-custom d-block">Lokasi Meja</span>
                            <span class="text-white"><?= htmlspecialchars($data['lokasi']) ?></span>
                        </div>

                        <div class="col-6">
                            <span class="text-muted-custom d-block">Tanggal & Waktu Reservasi</span>
                            <strong class="text-white"><?= htmlspecialchars($waktuFormatted) ?></strong>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted-custom d-block">Jumlah Tamu</span>
                            <strong class="text-white"><?= htmlspecialchars($data['jumlah_tamu']) ?> Orang</strong>
                        </div>

                        <div class="col-12">
                            <hr class="my-2 border-secondary opacity-50">
                        </div>

                        <div class="col-12 text-center">
                            <span class="text-muted-custom d-block" style="font-size: 0.8rem;">Dibuat Pada:
                                <?= htmlspecialchars($createdAtFormatted) ?></span>
                        </div>
                    </div>
                </div>


                <div class="d-flex justify-content-between align-items-center mt-4 no-print">
                    <a href="index.php" class="btn btn-outline-custom px-4 py-2">
                        Kembali ke Beranda
                    </a>
                    <button onclick="window.print()" class="btn btn-primary-custom px-4 py-2">
                        Cetak / Simpan Bukti
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
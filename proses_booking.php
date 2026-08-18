<?php
require_once __DIR__ . '/config/kuis_db.php';

// Pastikan request menggunakan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Sanitasi Input
$namaPemesan    = isset($_POST['nama_pemesan']) ? trim(htmlspecialchars($_POST['nama_pemesan'])) : '';
$noHp           = isset($_POST['no_hp']) ? trim(htmlspecialchars($_POST['no_hp'])) : '';
$email          = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
$idMeja         = isset($_POST['id_meja']) ? filter_var($_POST['id_meja'], FILTER_VALIDATE_INT) : 0;
$waktuInput     = isset($_POST['waktu_reservasi']) ? trim($_POST['waktu_reservasi']) : '';
$jumlahTamu     = isset($_POST['jumlah_tamu']) ? filter_var($_POST['jumlah_tamu'], FILTER_VALIDATE_INT) : 0;

// Function penanganan redirect error
function redirectWithError($msg) {
    header('Location: index.php?error=' . urlencode($msg));
    exit;
}

// 1. Validasi Input Wajib
if (empty($namaPemesan) || empty($noHp) || !$idMeja || empty($waktuInput) || !$jumlahTamu) {
    redirectWithError('Mohon lengkapi semua kolom form reservasi yang wajib diisi.');
}

// Format waktu reservasi ke standar DATETIME MySQL
$waktuReservasi = date('Y-m-d H:i:s', strtotime($waktuInput));

// 2. Validasi Keberadaan dan Status Meja
$stmtMeja = $pdo->prepare("SELECT * FROM meja WHERE id_meja = :id_meja");
$stmtMeja->execute([':id_meja' => $idMeja]);
$meja = $stmtMeja->fetch();

if (!$meja) {
    redirectWithError('Meja yang dipilih tidak ditemukan di dalam sistem.');
}

if ($meja['status'] !== 'Tersedia') {
    redirectWithError("Meja {$meja['nomor_meja']} saat ini sedang tidak dapat dipesan (Status: {$meja['status']}).");
}

// 3. Validasi Kapasitas Meja vs Jumlah Tamu
if ($jumlahTamu > (int)$meja['kapasitas']) {
    redirectWithError("Jumlah tamu ({$jumlahTamu} orang) melebihi kapasitas maksimal Meja {$meja['nomor_meja']} ({$meja['kapasitas']} orang).");
}

// 4. Validasi Bentrok Jadwal Pemesanan (+/- 2 Jam pada meja yang sama)
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
    redirectWithError("Meja {$meja['nomor_meja']} telah dipesan untuk waktu yang berdekatan pada tanggal/jam tersebut. Silakan pilih waktu atau meja lain.");
}

// 5. Simpan Data Reservasi & Update Status Meja Menggunakan Prepared Statements
try {
    $pdo->beginTransaction();

    $stmtInsert = $pdo->prepare("
        INSERT INTO reservasi (nama_pemesan, no_hp, email, id_meja, waktu_reservasi, jumlah_tamu, status_booking) 
        VALUES (:nama_pemesan, :no_hp, :email, :id_meja, :waktu_reservasi, :jumlah_tamu, 'Pending')
    ");
    
    $stmtInsert->execute([
        ':nama_pemesan'     => $namaPemesan,
        ':no_hp'            => $noHp,
        ':email'           => $email,
        ':id_meja'          => $idMeja,
        ':waktu_reservasi'  => $waktuReservasi,
        ':jumlah_tamu'      => $jumlahTamu
    ]);

    $idReservasiBaru = $pdo->lastInsertId();

    // Update status meja menjadi 'Terisi'
    $stmtUpdateMeja = $pdo->prepare("UPDATE meja SET status = 'Terisi' WHERE id_meja = :id_meja");
    $stmtUpdateMeja->execute([':id_meja' => $idMeja]);

    $pdo->commit();

    // Redirect ke halaman konfirmasi
    header("Location: konfirmasi.php?id=" . $idReservasiBaru);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    redirectWithError('Terjadi kesalahan sistem saat menyimpan data reservasi. Silakan coba kembali.');
}

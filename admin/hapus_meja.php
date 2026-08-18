<?php
require_once __DIR__ . '/../config/kuis_db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: meja.php');
    exit;
}

$idMeja = isset($_POST['id_meja']) ? filter_var($_POST['id_meja'], FILTER_VALIDATE_INT) : 0;

if ($idMeja) {
    try {
        // Ambil nomor meja untuk pesan respon
        $stmtMeja = $pdo->prepare("SELECT nomor_meja FROM meja WHERE id_meja = :id");
        $stmtMeja->execute([':id' => $idMeja]);
        $nomorMeja = $stmtMeja->fetchColumn();

        // Hapus meja (FOREIGN KEY ON DELETE CASCADE akan menghapus reservasi terkait secara otomatis)
        $stmtDelete = $pdo->prepare("DELETE FROM meja WHERE id_meja = :id");
        $stmtDelete->execute([':id' => $idMeja]);

        header('Location: meja.php?msg=' . urlencode("Meja '{$nomorMeja}' berhasil dihapus dari sistem."));
        exit;
    } catch (PDOException $e) {
        header('Location: meja.php?err=' . urlencode('Gagal menghapus meja: Terjadi kesalahan database.'));
        exit;
    }
} else {
    header('Location: meja.php?err=' . urlencode('ID Meja tidak valid.'));
    exit;
}

<?php
$host = 'localhost';
$dbname = 'restorant';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("
        <div style='font-family: Arial, sans-serif; padding: 30px; max-width: 600px; margin: 50px auto; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; color: #721c24;'>
            <h2 style='margin-top: 0;'>Gagal Terhubung ke Database</h2>
            <p>Sistem tidak dapat terhubung ke database <strong>restorant</strong>.</p>
            <p><strong>Panduan Perbaikan:</strong></p>
            <ul>
                <li>Pastikan MySQL di <strong>XAMPP Control Panel</strong> sudah dalam status Running.</li>
                <li>Pastikan database <code>restorant</code> sudah diimpor menggunakan file <code>db_restoran.sql</code>.</li>
            </ul>
        </div>
    ");
}

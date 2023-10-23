<?php
include 'inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $user_id = $_SESSION['user_id'];
    $tanggal = $_POST['tanggal'];
    $alamat_awal = $_POST['alamat_awal'];
    $alamat_tujuan = $_POST['alamat_tujuan'];
    $km_awal = $_POST['km_awal'];
    $km_akhir = $_POST['km_akhir'];
    $jenis_perjalanan = $_POST['jenis_perjalanan'];

    // Validasi input sesuai dengan kebutuhan Anda
    if (empty($tanggal) || empty($alamat_awal) || empty($alamat_tujuan) || empty($km_awal) || empty($km_akhir) || empty($jenis_perjalanan)) {
        echo "Harap isi semua kolom yang diperlukan.";
        exit();
    }

    // Anda dapat menambahkan validasi lebih lanjut di sini jika diperlukan.

    // Lanjutkan dengan proses penyimpanan ke database
    $query = "INSERT INTO laporan (user_id, tanggal, alamat_awal, alamat_tujuan, km_awal, km_akhir, jenis_perjalanan) VALUES ('$user_id', '$tanggal', '$alamat_awal', '$alamat_tujuan', '$km_awal', '$km_akhir', '$jenis_perjalanan')";

    if ($conn->query($query) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>
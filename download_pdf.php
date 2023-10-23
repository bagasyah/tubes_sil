<?php
session_start(); // Pastikan untuk memanggil session_start() di awal script jika belum dilakukan
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = null; // Jika user belum login, variabel $user_id diisi dengan null
}
require('inc/db.php');
require('fpdf.php');

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Buat objek PDF
$pdf = new FPDF();
$pdf->AddPage();

if (isset($_GET['id'])) {
    // Mendownload satu dokumen laporan berdasarkan ID
    $id = $_GET['id'];
    $query = "SELECT * FROM laporan INNER JOIN users ON laporan.user_id = users.id WHERE laporan.id = $id";

    $result = $conn->query($query);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Nama file
        $filename = "laporan_perjalanan_" . $row['id'] . ".pdf";

        // Tulis data dari database
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Laporan Perjalanan', 0, 1, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        $header = array('Nama', 'Tanggal', 'Alamat Awal', 'Alamat Tujuan', 'Jenis Perjalanan');
        foreach ($header as $col) {
            $pdf->Cell(37, 10, $col, 1);
        }
        $pdf->Ln();
        $pdf->Cell(37, 10, $row['username'], 1);
        $pdf->Cell(37, 10, $row['tanggal'], 1);
        $pdf->Cell(37, 10, $row['alamat_awal'], 1);
        $pdf->Cell(37, 10, $row['alamat_tujuan'], 1);
        $pdf->Cell(37, 10, $row['jenis_perjalanan'], 1);
        $pdf->Ln();
        $pdf->Ln();
        // Tabel KM awal, KM akhir, dan Total KM
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFont('Arial', '', 10);
        $header_km = array('KM Awal', 'KM Akhir', 'Total KM');
        foreach ($header_km as $col) {
            $pdf->Cell(37, 10, $col, 1);
        }
        $pdf->Ln();

        $pdf->Cell(37, 10, $row['km_awal'], 1);
        $pdf->Cell(37, 10, $row['km_akhir'], 1);
        $total_km = $row['km_akhir'] - $row['km_awal'];
        $pdf->Cell(37, 10, $total_km, 1);
        $pdf->Ln();

        // Spasi antara setiap baris data
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        // Output PDF
        $pdf->Output('D', $filename); // Menampilkan dialog download

        exit;
    } else {
        echo "<p>Dokumen laporan tidak ditemukan.</p>";
    }
} else {
    // Mendownload semua dokumen laporan dalam satu PDF
    $query = "SELECT * FROM laporan INNER JOIN users ON laporan.user_id = users.id";

    if (!empty($search_query)) {
        $query .= " WHERE tanggal LIKE '%$search_query%' OR alamat_awal LIKE '%$search_query%' OR alamat_tujuan LIKE '%$search_query%' OR username LIKE '%$search_query%'";
    }
    if (isset($user_id)) { // Pastikan variabel $user_id sudah berisi ID user yang telah login
        // Tambahkan kondisi untuk hanya mengambil data yang dimiliki oleh user yang login
        $query .= " AND laporan.user_id = $user_id";
    }
    $query .= " ORDER BY laporan.id DESC";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        // Tulis header tabel
        // ... Kode untuk menulis header tabel ...
        $total_distance = 0;
        while ($row = $result->fetch_assoc()) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Laporan Perjalanan', 0, 1, 'C');
            $pdf->Ln();

            // ... Kode untuk menulis data laporan ...

            // Tabel utama
            $pdf->SetFont('Arial', '', 10);
            $header = array('Nama', 'Tanggal', 'Alamat Awal', 'Alamat Tujuan', 'Jenis Perjalanan');
            foreach ($header as $col) {
                $pdf->Cell(37, 10, $col, 1);
            }
            $pdf->Ln();
            $pdf->Cell(37, 10, $row['username'], 1);
            $pdf->Cell(37, 10, $row['tanggal'], 1);
            $pdf->Cell(37, 10, $row['alamat_awal'], 1);
            $pdf->Cell(37, 10, $row['alamat_tujuan'], 1);
            $pdf->Cell(37, 10, $row['jenis_perjalanan'], 1);
            $pdf->Ln();
            $pdf->Ln();
            // Tabel KM awal, KM akhir, dan Total KM
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFont('Arial', '', 10);
            $header_km = array('KM Awal', 'KM Akhir', 'Total KM');
            foreach ($header_km as $col) {
                $pdf->Cell(37, 10, $col, 1);
            }
            $pdf->Ln();
            $pdf->Cell(37, 10, $row['km_awal'], 1);
            $pdf->Cell(37, 10, $row['km_akhir'], 1);
            $total_km = $row['km_akhir'] - $row['km_awal'];
            $pdf->Cell(37, 10, $total_km, 1);
            $pdf->Ln();
            $total_distance += $total_km;
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
        }
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Ln();
        $pdf->Cell(0, 10, 'Total Jarak Tempuh: ' . $total_distance . ' km', 0, 1, 'L');
        $pdf->Ln();
        // Output PDF
        $pdf->Output('D', 'laporan_perjalanan_all.pdf'); // Menampilkan dialog download


        exit;
    } else {
        echo "<p>Tidak ada data yang dapat diunduh.</p>";
    }
}
?>
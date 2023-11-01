<?php
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

if (isset($_GET['id'])) {
    // Mendapatkan data laporan berdasarkan ID
    $id = $_GET['id'];
    $query = "SELECT * FROM laporan INNER JOIN users ON laporan.user_id = users.id WHERE laporan.id = $id";
} else {
    // Mendapatkan semua data laporan
    $query = "SELECT * FROM laporan INNER JOIN users ON laporan.user_id = users.id";

    if (!empty($search_query)) {
        $query .= " WHERE tanggal LIKE '%$search_query%' OR alamat_awal LIKE '%$search_query%' OR alamat_tujuan LIKE '%$search_query%' OR username LIKE '%$search_query%'";
    }
}

$result = $conn->query($query);
function calculateBBM($jenis_perjalanan, $total_km)
{
    $bbm_per_km = 1 / 10;
    $perkiraan_bbm = round($total_km * $bbm_per_km); // Bulatkan hasil jika koma
    return $perkiraan_bbm;
}
function calculatebiaya($perkiraan_bbm)
{
    $harga = 14000;
    $biaya = $perkiraan_bbm * $harga;
    return $biaya;
}
if ($result->num_rows > 0) {
    if (isset($_GET['id'])) {
        // Mendownload satu dokumen laporan
        $row = $result->fetch_assoc();

        // Nama file
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
        // Mendownload semua dokumen laporan dalam satu PDF
        // Nama file
        $filename = "laporan_perjalanan_" . date('Ymd') . ".pdf";

        // Buat objek PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Laporan Perjalanan', 0, 1, 'C');
        $pdf->Ln();
        // Tulis data dari database
        $counter = 0;
        while ($row = $result->fetch_assoc()) {


            // ... Kode untuk menulis data laporan ...

            // Tabel utama
            $pdf->SetFont('Arial', 'B', 10);
            $header = array('Nama', 'Tanggal', 'Alamat Awal', 'Alamat Tujuan', 'Jenis Perjalanan');
            foreach ($header as $col) {
                $pdf->Cell(37, 10, $col, 1);
            }
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(37, 10, $row['username'], 1);
            $pdf->Cell(37, 10, $row['tanggal'], 1);
            $pdf->Cell(37, 10, $row['alamat_awal'], 1);
            $pdf->Cell(37, 10, $row['alamat_tujuan'], 1);
            $pdf->Cell(37, 10, $row['jenis_perjalanan'], 1);
            $pdf->Ln();
            // Tabel KM awal, KM akhir, dan Total KM
            $pdf->SetFont('Arial', 'B', 10);
            $header_km = array('KM Awal', 'KM Akhir', 'Total KM', 'Perkiraan BBM', 'Biaya');
            foreach ($header_km as $col) {
                $pdf->Cell(37, 10, $col, 1);
            }
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(37, 10, $row['km_awal'] . ' km', 1);
            $pdf->Cell(37, 10, $row['km_akhir'] . ' km', 1);
            $total_km = $row['km_akhir'] - $row['km_awal'];
            $pdf->Cell(37, 10, $total_km . ' km', 1);
            $perkiraan_bbm = calculateBBM($row['jenis_perjalanan'], $total_km);
            $biaya = calculatebiaya($perkiraan_bbm);
            $pdf->Cell(37, 10, $perkiraan_bbm . ' Liter', 1);
            $pdf->Cell(37, 10, 'Rp.' . $biaya, 1);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
        }

        $pdf->Ln();
        // Output PDF
        $pdf->Output('D', $filename); // Menampilkan dialog download

        exit;
    }
} else {
    echo "<p>Tidak ada data yang dapat diunduh.</p>";
}
?>
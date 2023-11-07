<?php
include 'inc/db.php';

$id = $_GET['id'];
$query = "SELECT * FROM laporan WHERE id='$id'";
$result = $conn->query($query);
$row = $result->fetch_assoc();

if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $tanggal = $_POST['tanggal'];
    $alamat_awal = $_POST['alamat_awal'];
    $alamat_tujuan = $_POST['alamat_tujuan'];
    $km_awal = $_POST['km_awal'];
    $km_akhir = $_POST['km_akhir'];
    $no_polisi = $_POST['no_polisi'];
    $tipe_mobil = $_POST['tipe_mobil'];
    $jenis_perjalanan = $_POST['jenis_perjalanan'];
    // No new photo uploaded, update other fields in the database
    $query = "UPDATE laporan SET tanggal='$tanggal', alamat_awal='$alamat_awal', alamat_tujuan='$alamat_tujuan', km_awal='$km_awal', km_akhir='$km_akhir',jenis_perjalanan='$jenis_perjalanan' WHERE id='$id'";
    if ($conn->query($query) === TRUE) {
        header("Location: data_perjalanan_user.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Laporan Perjalanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .container {
            padding: 30px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .form-control {
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input[type="radio"],
        .form-group input[type="checkbox"] {
            margin-right: 5px;
        }

        .btn {
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Laporan Perjalanan</h2>
        <form method="POST" action="edit.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <div class="form-group">
                <label for="tanggal">Tanggal:</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal"
                    value="<?php echo $row['tanggal']; ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat_awal">Alamat Awal:</label>
                <input type="text" class="form-control" id="alamat_awal" name="alamat_awal"
                    value="<?php echo $row['alamat_awal']; ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat_tujuan">Alamat Tujuan:</label>
                <input type="text" class="form-control" id="alamat_tujuan" name="alamat_tujuan"
                    value="<?php echo $row['alamat_tujuan']; ?>" required>
            </div>
            <div class="form-group">
                <label for="km_awal">KM Awal:</label>
                <input type="number" class="form-control" id="km_awal" name="km_awal"
                    value="<?php echo $row['km_awal']; ?>" required>
            </div>
            <div class="form-group">
                <label for="km_akhir">KM Akhir:</label>
                <input type="number" class="form-control" id="km_akhir" name="km_akhir"
                    value="<?php echo $row['km_akhir']; ?>" required>
            </div>
            <div class="form-group">
                <label for="jenis_perjalanan">Jenis Perjalanan :</label>
                <select class="form-control" id="jenis_perjalanan" name="jenis_perjalanan" required>
                    <option value="luar" <?php if ($row['jenis_perjalanan'] === 'luar')
                        echo 'selected'; ?>>luar</option>
                    <option value="dalam" <?php if ($row['jenis_perjalanan'] === 'dalam')
                        echo 'selected'; ?>>dalam
                    </option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            <button type="button" class="btn btn-danger" onclick="window.location.href='data_perjalanan_user.php'">Cancel</button>
        </form>
    </div>
</body>

</html>
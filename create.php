<?php include 'inc/db.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Buat Laporan Perjalanan</title>
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

        .fot {
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mt-3">Laporan Perjalanan</h2>
        <form method="POST" action="create_process.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="tanggal">Tanggal:</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label for="alamat_awal">Alamat Awal:</label>
                <input type="text" class="form-control" id="alamat_awal" name="alamat_awal" required>
            </div>
            <div class="form-group">
                <label for="alamat_tujuan">Alamat Tujuan:</label>
                <input type="text" class="form-control" id="alamat_tujuan" name="alamat_tujuan" required>
            </div>
            <div class="form-group">
                <label for="km_awal">KM Awal:</label>
                <input type="number" class="form-control" id="km_awal" name="km_awal" required>
            </div>
            <div class="form-group">
                <label for="km_akhir">KM Akhir:</label>
                <input type="number" class="form-control" id="km_akhir" name="km_akhir">
            </div>
            <div class="form-group">
                <label for="jenis_perjalanan">Jenis Perjalanan:</label>
                <select class="form-control" id="jenis_perjalanan" name="jenis_perjalanan" required>
                    <option value="luar">Luar</option>
                    <option value="dalam">Dalam</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success" name="submit">Submit</button>
                <button type="button" class="btn btn-danger"
                    onclick="window.location.href='statistik_user.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>

</html>
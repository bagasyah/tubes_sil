<!DOCTYPE html>
<html>

<head>
    <title>Laporan Perjalanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navbar {
            background-color: #70cce1;
        }

        .search-form {
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            width: 100%;
        }

        .navbar-brand .separator {
            border-right: 2px solid #fff;
            width: 100%;
        }

        .dropdown-toggle:focus {
            box-shadow: none;
        }

        table {}

        .details-content {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        /* CSS yang sudah ada tetap sama */

        .sort-container {
            display: flex;
            align-items: center;
        }

        .sort-label {
            margin-right: 10px;
        }

        .sort-options select {
            width: auto;
            margin-bottom: 5px;
        }

        @media (max-width: 576px) {
            /* CSS untuk tampilan mobile tetap sama */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="statistik_user.php">
            SAFETY DRIVE
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
            </ul>
            <ul class="navbar-nav ml-auto mt-1">
                <?php
                echo "<li class='nav-item'><a class='nav-link btn mb-1 mr-2 text-light btn-success' href='statistik_user.php'><i class='fas fa-chevron-left'></i> Back</a></li>";
                echo "<li class='nav-item'><a class='nav-link btn mb-1 mr-2 text-light btn-danger' href='logout.php'><i class='fas fa-sign-out-alt'></i> Logout</a></li>";
                ?>
            </ul>
        </div>
    </nav>
    <div class="container">

        <?php
        include 'inc/db.php';
        // Periksa koneksi
        if ($conn->connect_error) {
            die("Koneksi ke database gagal: " . $conn->connect_error);
        }

        // Deklarasi variabel total_km_all di luar loop while
        $total_km_all = 0;

        // Deklarasi fungsi calculateBBM di luar loop while
        
        // Kode query dan tampilan HTML
        $search_query = "";
        if (isset($_GET['search'])) {
            $search_query = $_GET['search'];
        }

        // Baca parameter sort dari URL
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

        $query = "SELECT users.id, users.username, SUM(laporan.km_akhir - laporan.km_awal) AS total_km
        FROM users
        LEFT JOIN laporan ON users.id = laporan.user_id
        WHERE users.username LIKE '%$search_query%' OR laporan.km_awal LIKE '%$search_query%' OR laporan.km_akhir LIKE '%$search_query%'
        GROUP BY users.id, users.username
        HAVING total_km > 0";

        // Modifikasi query sesuai dengan penggunaan parameter sort
        if ($sort == 'terbanyak') {
            $query .= " ORDER BY total_km DESC";
        } elseif ($sort == 'terendah') {
            $query .= " ORDER BY total_km ASC";
        } else {
            $query .= " ORDER BY laporan.id DESC"; // Default sorting
        }

        $totalRowsResult = $conn->query($query);
        $totalRows = $totalRowsResult->num_rows;

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

        // Eksekusi query ke database
        $result = $conn->query($query);

        // Tampilkan tampilan HTML untuk menampilkan hasil query
        echo "<h2 class='mt-1'>Semua Laporan Perjalanan</h2>";
        echo "<form class='mb-3' method='GET'>";
        echo "<div class='input-group'>";
        echo "<input type='text' class='form-control' name='search' placeholder='Cari akun pengguna...'>";
        echo "<div class='input-group-append'>";
        echo "<button class='btn btn-primary' type='submit'><i class='fas fa-search'></i></button>";
        echo "<button class='btn btn-danger ml-1' type='reset' onclick='window.location.href=\"data_perjalanan.php\"'><i class='fas fa-sync'></i></button>";
        echo "</div>";
        echo "</div>";
        echo "</form>";
        // Tambahkan dropdown untuk pengurutan
        echo "<div class='sort-container'>";
        echo "<label class='sort-label' for='sort-select'>Sort by:</label>";
        echo "<form method='GET' action='data_perjalanan.php'>";
        echo "<div class='sort-options'>";
        echo "<select id='sort-select' name='sort' onchange='this.form.submit()'>";
        echo "<option value='default' " . ($sort == 'default' ? 'selected' : '') . ">Default</option>";
        echo "<option value='terbanyak' " . ($sort == 'terbanyak' ? 'selected' : '') . ">Total KM Terbanyak</option>";
        echo "<option value='terendah' " . ($sort == 'terendah' ? 'selected' : '') . ">Total KM Terendah</option>";
        echo "</select>";
        echo "</div>";
        echo "</form>";
        echo "</div>";

        // Eksekusi query ke database
        $result = $conn->query($query);

        // Mulai menampilkan tabel dan data perjalanan
        echo "<div class='table-responsive table-responsive-sm'>";
        echo "<table class='table'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Username</th>";
        echo "<th>Jumlah Data Perjalanan</th>";
        echo "<th>Total KM</th>";
        echo "<th>Actions</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        // Reset ulang result set
        $result->data_seek(0);

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            // Hitung jumlah data perjalanan
            $userId = $row['id'];
            $jumlahDataPerjalananQuery = "SELECT COUNT(*) AS jumlah_data FROM laporan WHERE user_id = $userId";
            $jumlahDataResult = $conn->query($jumlahDataPerjalananQuery);
            $jumlahDataRow = $jumlahDataResult->fetch_assoc();
            $jumlahDataPerjalanan = $jumlahDataRow['jumlah_data'];

            echo "<td>" . $jumlahDataPerjalanan . " Perjalanan</td>";
            echo "<td>" . $row['total_km'] . "</td>";
            echo "<td>";
            echo "<a href='detail_akun.php?id=" . $row['id'] . "' class='btn btn-info '><i class='fas fa-arrow-right'></i></a>";
            echo "</td>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
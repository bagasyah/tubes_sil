<!DOCTYPE html>
<html>

<head>
    <title>SAFETY DRIVE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Atur warna latar belakang dan teks pada navbar */
        .navbar {
            background-color: #70cce1;
            color: #fff;
        }

        /* Atur margin pada navbar */
        .navbar {
            margin-bottom: 20px;
        }

        /* Atur ukuran font pada tombol navbar */
        .navbar .btn {
            font-size: 14px;
        }

        /* Atur margin pada container */
        .container {
            margin-top: 20px;
        }

        /* Atur ukuran font pada judul halaman */
        h2 {
            font-size: 24px;
            color: #007bff;
        }

        /* Atur ukuran font pada teks konten halaman */
        p {
            font-size: 16px;
        }

        /* Atur margin pada tombol edit dan delete */
        .table .btn {
            margin: 2px;
        }

        /* Atur lebar gambar pada tabel */
        .table td img {
            max-width: 100px;
            height: auto;
        }

        /* Atur margin pada form pencarian */
        .search-form {
            margin-bottom: 20px;
        }

        /* Atur tata letak navbar pada perangkat mobile */
        @media (max-width: 576px) {
            .navbar-brand {
                margin-right: 0;
            }

            .navbar-nav.ml-auto.mt-1 {
                margin-top: 0;
            }

            .nav-link.btn.mb-1.text-light.btn-primary {
                margin-bottom: 0.5rem;
            }

            /* Atur lebar navbar collapse pada perangkat mobile */
            .navbar-collapse {
                width: 100%;
            }

            /* Atur tata letak tombol navbar pada perangkat mobile */
            .navbar-toggler {
                margin-top: 0.5rem;
            }

            /* Atur tata letak tombol pada perangkat mobile */
            .navbar-nav .btn {
                display: block;
                margin-bottom: 0.5rem;
                width: 100%;
            }
        }

        /* Atur tampilan tombol tambah perjalanan */
        .btn-tambah {
            background-color: #28a745;
            color: #fff;
        }

        /* Atur tampilan tombol edit perjalanan */
        .btn-edit {
            background-color: #ffc107;
            color: #212529;
        }

        /* Atur tampilan tombol hapus perjalanan */
        .btn-hapus {
            background-color: #dc3545;
            color: #fff;
        }

        /* Atur tampilan link foto */
        .link-foto {
            color: #007bff;
            cursor: pointer;
        }

        .navbar-brand .separator {
            border-right: 2px solid #fff;
            width: 100%;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="dashboard.php">
            SAFETY DRIVE
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto mr-3 mt-1">
                <?php
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header("Location: index.php");
                }

                $role = $_SESSION['role'];


                if ($role == 'user') {
                    echo "<li class='nav-item'><a class='nav-link btn mb-1 mr-2 text-light btn-tambah' href='create.php'><i class='fas fa-plus'></i> Tambah Perjalanan</a></li>";
                    echo "<li class='nav-item'><a class='nav-link btn mb-1 mr-2 text-light btn-success' href='data_perjalanan.php'><i class='fas fa-car'></i> Data Perjalanan</a></li>";
                } elseif ($role == 'admin') {
                    echo "<li class='nav-item'><a class='nav-link btn mb-1 mr-2 text-light btn-success' href='akun_user.php'><i class='fas fa-users'></i> Kelola Pengguna</a></li>";
                }
                echo "<li class='nav-item'><a class='nav-link btn mb-1 text-light btn-danger' href='logout.php'><i class='fas fa-sign-out-alt'></i> Logout</a></li>";
                ?>
            </ul>
        </div>
    </nav>

    <div class="container table-responsive">
        <?php
        include 'inc/db.php';

        $user_id = $_SESSION['user_id'];

        if ($role == 'user') {
            $user_query = "SELECT username FROM users WHERE id='$user_id'";
            $user_result = $conn->query($user_query);
            if ($user_result->num_rows > 0) {
                $user_row = $user_result->fetch_assoc();
                $username = $user_row['username'];

                echo "<h2>Profil User</h2>";
                echo "<p>Nama: $username</p>";

                // Menghitung total jarak tempuh dari data yang dicari
                $tanggal_awal = $_GET['tanggal_awal'] ?? '';
                $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

                // Fitur pencarian di atas pencarian tanggal
                $keyword = $_GET['keyword'] ?? '';

                $total_km_pencarian_query = "SELECT SUM(km_akhir - km_awal) AS total_km_pencarian FROM laporan WHERE user_id='$user_id'";

                // Tambahkan kondisi tanggal_awal dan tanggal_akhir jika ada pencarian tanggal
                if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                    $total_km_pencarian_query .= " AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                }

                // Tambahkan kondisi pencarian berdasarkan keyword
                if (!empty($keyword)) {
                    $total_km_pencarian_query .= " AND (alamat_awal LIKE '%$keyword%' OR alamat_tujuan LIKE '%$keyword%')";
                }

                $total_km_pencarian_result = $conn->query($total_km_pencarian_query);
                $total_km_pencarian_row = $total_km_pencarian_result->fetch_assoc();
                $total_km_pencarian = $total_km_pencarian_row['total_km_pencarian'];

                echo "<p>Total Jarak Tempuh : $total_km_pencarian KM</p>";
            }

            $query = "SELECT * FROM laporan WHERE user_id='$user_id'";

            // Tambahkan kondisi tanggal_awal dan tanggal_akhir jika ada pencarian tanggal
            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $query .= " AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
            }

            // Tambahkan kondisi pencarian berdasarkan keyword
            if (!empty($keyword)) {
                $query .= " AND (alamat_awal LIKE '%$keyword%' OR alamat_tujuan LIKE '%$keyword%')";
            }
            $query .= " ORDER BY laporan.id DESC";
            $result = $conn->query($query);
            $totalRowsResult = $conn->query($query);
            $totalRows = $totalRowsResult->num_rows;

            // Define jumlah baris per halaman
            $rowsPerPage = 5;

            // Dapatkan nomor halaman saat ini dari query string
            $currentPage = 1;
            if (isset($_GET['page']) && is_numeric($_GET['page'])) {
                $currentPage = $_GET['page'];
            }

            // Hitung total jumlah halaman
            $totalPages = ceil($totalRows / $rowsPerPage);

            // Pastikan currentPage tidak lebih dari totalPages dan tidak negatif
            $currentPage = max(1, min($currentPage, $totalPages));

            // Hitung nomor baris awal untuk halaman saat ini
            $startRow = ($currentPage - 1) * $rowsPerPage;

            // Tambahkan klausa LIMIT ke dalam query SQL
            $query .= " LIMIT $startRow, $rowsPerPage";

            $result = $conn->query($query);
            echo "<h2>Laporan Perjalanan</h2>";

            echo "<div class='search-form'>";
            echo "<form method='GET' action='dashboard.php'>";
            echo "<div class='form-row'>";
            echo "<div class='form-group col-md-4'>";
            echo "<label for='keyword'>Cari Alamat:</label>";
            echo "<input type='text' class='form-control' name='keyword' id='keyword' value='$keyword'>";
            echo "</div>";
            echo "<div class='form-group col-md-4'>";
            echo "<label for='tanggal_awal'>Tanggal Awal:</label>";
            echo "<input type='date' class='form-control' name='tanggal_awal' id='tanggal_awal' value='$tanggal_awal'>";
            echo "</div>";
            echo "<div class='form-group col-md-4'>";
            echo "<label for='tanggal_akhir'>Tanggal Akhir:</label>";
            echo "<input type='date' class='form-control' name='tanggal_akhir' id='tanggal_akhir' value='$tanggal_akhir'>";
            echo "</div>";
            echo "</div>";
            echo "<button class='btn btn-primary' type='submit'><i class='fas fa-search'></i></button>";
            echo "<button class='btn btn-danger ml-1' type='reset' onclick='window.location.href=\"dashboard.php\"'><i class='fas fa-sync'></i></button>";
            echo "<a href='download_pdf.php' class='btn btn-success ml-1'><i class='fas fa-file-pdf'></i></a>";
            echo "<a href='download_excel.php' class='btn btn-success ml-1'><i class='fas fa-file-excel'></i></a>";

            echo "</form>";
            echo "</div>";

            if ($result->num_rows > 0) {
                echo "<div class='table-responsive'>";
                echo "<table class='table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Tanggal</th>";
                echo "<th>Alamat Awal</th>";
                echo "<th>Alamat Tujuan</th>";
                echo "<th>KM Awal</th>";
                echo "<th>KM Akhir</th>";
                echo "<th>Total KM</th>";
                echo "<th>Jenis Perjalanan</th>";
                //echo "<th>Status</th>";
                echo "<th>Actions</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['tanggal'] . "</td>";
                    echo "<td>" . $row['alamat_awal'] . "</td>";
                    echo "<td>" . $row['alamat_tujuan'] . "</td>";
                    echo "<td>" . $row['km_awal'] . "</td>";
                    echo "<td>" . $row['km_akhir'] . "</td>";
                    $total_km = $row['km_akhir'] - $row['km_awal'];
                    echo "<td>" . $total_km . "</td>";
                    echo "<td>" . $row['jenis_perjalanan'] . "</td>";
                    // Perhitungan perkiraan BBM berdasarkan jenis perjalanan dan tipe mobil
                    $jenis_perjalanan = $row['jenis_perjalanan'];
                    echo "<td>";
                    echo "<a href='edit.php?id=" . $row['id'] . "' class='btn btn-primary'><i class='fas fa-pencil-alt'></i></span></a> ";
                    echo "<a href='delete.php?id=" . $row['id'] . "' class='btn btn-danger'><i class='fas fa-trash-alt'></i></span></a> ";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                if ($totalPages > 0) {
                    echo "<nav aria-label='Page navigation'>";
                    echo "<ul class='pagination justify-content-center'>";

                    // Tambahkan tombol Back (Previous) jika currentPage > 1
                    if ($currentPage > 1) {
                        echo "<li class='page-item'><a class='page-link' href='dashboard.php?page=" . ($currentPage - 1) . "'>&laquo;</a></li>";
                    }

                    for ($page = 1; $page <= $totalPages; $page++) {
                        $activeClass = ($page == $currentPage) ? " active" : "";
                        echo "<li class='page-item$activeClass'><a class='page-link' href='dashboard.php?page=$page'>$page</a></li>";
                    }

                    // Tambahkan tombol Next jika currentPage < totalPages
                    if ($currentPage < $totalPages) {
                        echo "<li class='page-item'><a class='page-link' href='dashboard.php?page=" . ($currentPage + 1) . "'>&raquo;</a></li>";
                    }

                    echo "</ul>";
                    echo "</nav>";
                } else {
                    echo "<p>Belum ada laporan perjalanan.</p>";
                }
            } else {
                echo "Belum ada laporan perjalanan.";
            }
        } elseif ($role == 'admin') {
            $search_query = "";
            if (isset($_GET['search'])) {
                $search_query = $_GET['search'];
            }

            $query = "SELECT * FROM laporan INNER JOIN users ON laporan.user_id = users.id";

            // Tambahkan kondisi pencarian berdasarkan tanggal
            $tanggal_awal = $_GET['tanggal_awal'] ?? '';
            $tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

            if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                $query .= " WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
            }

            if (!empty($search_query)) {
                if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
                    $query .= " AND (tanggal LIKE '%$search_query%' OR alamat_awal LIKE '%$search_query%' OR alamat_tujuan LIKE '%$search_query%' OR username LIKE '%$search_query%')";
                } else {
                    $query .= " WHERE tanggal LIKE '%$search_query%' OR alamat_awal LIKE '%$search_query%' OR alamat_tujuan LIKE '%$search_query%' OR username LIKE '%$search_query%'";
                }
            }

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
            $query .= " ORDER BY tanggal DESC";

            $result = $conn->query($query);

            echo "<h2 class='mt-1'>Semua Laporan Perjalanan</h2>";
            echo "<div class='search-form'>";
            echo "<form method='GET' action='dashboard.php'>";
            echo "<div class='form-row'>";
            echo "<div class='form-group col-md-4'>";
            echo "<label for='search'>Cari:</label>";
            echo "<input type='text' class='form-control' name='search' id='search' value='$search_query'>";
            echo "</div>";
            echo "<div class='form-group col-md-4'>";
            echo "<label for='tanggal_awal'>Tanggal Awal:</label>";
            echo "<input type='date' class='form-control' name='tanggal_awal' id='tanggal_awal' value='$tanggal_awal'>";
            echo "</div>";
            echo "<div class='form-group col-md-4'>";
            echo "<label for='tanggal_akhir'>Tanggal Akhir:</label>";
            echo "<input type='date' class='form-control' name='tanggal_akhir' id='tanggal_akhir' value='$tanggal_akhir'>";
            echo "</div>";
            echo "</div>";
            echo "<button class='btn btn-primary' type='submit'><i class='fas fa-search'></i></button>";
            echo "<button class='btn btn-danger ml-1' type='reset' onclick='window.location.href=\"dashboard.php\"'><i class='fas fa-sync'></i></button>";
            echo "<a href='download_pdf2.php' class='btn btn-success ml-1'><i class='fas fa-file-pdf'></i></a>";
            echo "<a href='download_excel2.php' class='btn btn-success ml-1'><i class='fas fa-file-excel'></i></a>";
            echo "</form>";
            echo "</div>";

            $total_perjalanan = 0; // Inisialisasi total perjalanan
            if ($result->num_rows > 0) {
                echo "<div class='table-responsive table-responsive-sm'>";
                echo "<table class='table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>User</th>";
                echo "<th>Tanggal</th>";
                echo "<th>Alamat Awal</th>";
                echo "<th>Alamat Tujuan</th>";
                echo "<th>KM Awal</th>";
                echo "<th>KM Akhir</th>";
                echo "<th>Total KM</th>";
                echo "<th>Jenis Perjalanan</th>";
                echo "<th>Perkiraan BBM</th>";
                echo "<th>Biaya</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['tanggal'] . "</td>";
                    echo "<td>" . $row['alamat_awal'] . "</td>";
                    echo "<td>" . $row['alamat_tujuan'] . "</td>";
                    echo "<td>" . $row['km_awal'] . "</td>";
                    echo "<td>" . $row['km_akhir'] . "</td>";
                    $total_km = $row['km_akhir'] - $row['km_awal'];
                    $jenis_perjalanan = $row['jenis_perjalanan'];
                    $perkiraan_bbm = calculateBBM($jenis_perjalanan, $total_km);
                    $biaya = calculatebiaya($perkiraan_bbm);
                    echo "<td>" . $total_km . "</td>";
                    echo "<td>" . $jenis_perjalanan . "</td>";
                    echo "<td>" . $perkiraan_bbm . " Liter</td>";
                    echo "<td>RP." . $biaya . "</td>";
                    echo "<td>";
                    //echo "<a href='download_pdf2.php?id=" . $row['id'] . "' class='btn btn-success'>download</a>";
                    echo "</td>";
                    echo "</tr>"; // Tutup baris data saat ini
                    // Akumulasi total jarak tempuh untuk pencarian
                    $total_perjalanan += $total_km;
                }

                // Cetak total perjalanan setelah perulangan
                echo "<h4>Total Jarak Tempuh : " . $total_perjalanan . " KM</h4>";
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                echo "</div>";
            } else {
                echo "Belum ada laporan perjalanan.";
            }
        }
        $conn->close();
        ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="fotoModal" tabindex="-1" role="dialog" aria-labelledby="fotoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="" class="img-fluid" id="modalFoto">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#fotoModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var foto = button.data('foto');
            var modal = $(this);
            modal.find('.modal-body #modalFoto').attr('src', foto);
        });
    </script>
</body>

</html>
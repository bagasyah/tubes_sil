<!DOCTYPE html>
<html>

<head>
    <title>SAFETY DRIVE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/statistik_user.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="statistik_user.php">
            SAFETY DRIVE
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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

                echo "<li class='nav-item'><a class='nav-link btn mb-1 mr-2 text-light btn-tambah' href='create.php'><i class='fas fa-plus'></i> Tambah Perjalanan</a></li>";
                echo "<li class='nav-item'><a class='nav-link btn mb-1 mr-2 text-light btn-success' href='data_perjalanan.php'><i class='fas fa-car'></i> Data Perjalanan</a></li>";
                echo "<li class='nav-item'><a class='nav-link btn mb-1 text-light btn-danger' href='logout.php'><i class='fas fa-sign-out-alt'></i> Logout</a></li>";
                ?>
            </ul>
        </div>
    </nav>

    <div class="container table-responsive">
        <?php
        include 'inc/db.php';

        if (isset($_SESSION['role'])) {
            $allowed_roles = ['user'];
            if (!in_array($_SESSION['role'], $allowed_roles)) {
                header("Location: statistik_admin.php");
                exit();
            }
        } else {
            header("Location: statistik_admin.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $user_query = "SELECT username FROM users WHERE id='$user_id'";
        $user_result = $conn->query($user_query);
        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $username = $user_row['username'];
            function calculateBBM($total_km)
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

            $total_km_pencarian_query = "SELECT SUM(km_akhir - km_awal) AS total_km_pencarian FROM laporan WHERE user_id='$user_id'";
            $total_km_pencarian_result = $conn->query($total_km_pencarian_query);
            $total_km_pencarian_row = $total_km_pencarian_result->fetch_assoc();
            $total_km_pencarian = $total_km_pencarian_row['total_km_pencarian'];
            $total_km_pencarian = $total_km_pencarian ?? 0;

            echo '<h2>Profil Pengguna</h2>';
            echo "<h5>Nama : $username </h5>";
            echo "<h5>Role : $role </h5>";

            $total_luar_query = "SELECT COUNT(jenis_perjalanan) AS total_luar FROM `laporan` WHERE jenis_perjalanan LIKE '%r' AND user_id='$user_id'";
            $total_dalam_query = "SELECT COUNT(jenis_perjalanan) AS total_dalam FROM `laporan` WHERE jenis_perjalanan LIKE '%m' AND user_id='$user_id'";

            $total_dalam_result = $conn->query($total_dalam_query);
            $total_luar_result = $conn->query($total_luar_query);

            $total_dalam_row = $total_dalam_result->fetch_assoc();
            $total_luar_row = $total_luar_result->fetch_assoc();

            $total_dalam = $total_dalam_row['total_dalam'];
            $total_luar = $total_luar_row['total_luar'];

            $total_dalam = $total_dalam ?? 0;
            $total_luar = $total_luar ?? 0;

            $data_jenis_perjalanan = array($total_dalam, $total_luar);

            $frekuensi_input_query = "SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, COUNT(*) AS jumlah_data FROM laporan WHERE user_id='$user_id' GROUP BY bulan;";
            $frekuensi_input_result = $conn->query($frekuensi_input_query);

            $bulanArray = array();
            $frekuensiArray = array();

            while ($frekuensi_input_row = $frekuensi_input_result->fetch_assoc()) {
                $bulan = $frekuensi_input_row['bulan'];
                $jumlah_data = $frekuensi_input_row['jumlah_data'];

                $bulanArray[] = $bulan;
                $jumlah_dataArray[] = $jumlah_data;
            }
        }
        $conn->close();
        ?>
    </div>

    <div class="container table-responsive">
        <h2>Statistik Pengguna</h2>
        <div class="data-board">
            <div class="data-item">
                <h6 class="dats">Total Tempuh Perjalanan</h6>
                <?php echo "<h7>$total_km_pencarian KM </h7>" ?>
            </div>
            <div class="data-item">
                <h6 class="dats">Total Penggunaan BBM</h6>
                <?php
                $total_bbm = calculateBBM($total_km_pencarian);
                echo "<h7>$total_bbm Liter</h7>";
                ?>
            </div>
            <div class="data-item">
                <h6 class="dats">Total Biaya Perjalanan</h6>
                <?php
                $total_biaya = calculatebiaya($total_bbm);
                echo "<h7>Rp.$total_biaya,00</h7>";
                ?>
            </div>
        </div>
    </div>

    <div class="container table-responsive">
        <div class="stats-board">
            <div class="stats-item">
                <h6 class="stats">Jenis Perjalanan Yang Dilakukan</h6>
                <canvas id="stats-jenis" class="piechart" style="width:100%; height: 100%;"></canvas>
            </div>
            <div class="stats-item">
                <h6 class="stats">Frekuensi Input</h6>
                <canvas id="stats-tanggal" class="linechart" style="width:100%; height: 100%;"></canvas>
            </div>
        </div>
    </div>

    <script>
        var xValues1 = ["Dalam Kota", "Luar Kota"]
        var yValues1 = <?php echo json_encode($data_jenis_perjalanan); ?>;
        var barColors1 = [
            "#b91d47",
            "#e8c3b9"
        ];

        new Chart("stats-jenis", {
            type: "pie",
            data: {
                labels: xValues1,
                datasets: [{
                    backgroundColor: barColors1,
                    data: yValues1
                }]
            },
            options: {
                title: {
                    display: true,
                }
            }
        });

        var bulan_input = <?php echo json_encode($bulanArray); ?>;
        var frekuensi_input = <?php echo json_encode($jumlah_dataArray); ?>;
        var barColors2 = [
            "#b91d47"
        ];

        new Chart("stats-tanggal", {
            type: "line",
            data: {
                labels: bulan_input,
                datasets: [{
                    fill: true,
                    lineTension: 0,
                    backgroundColor: barColors2,
                    borderColor: "rgba(255,255,255,0,1)",
                    data: frekuensi_input
                }]
            },
            options: {
                legend: {
                    display: false,
                }
            }
        });
    </script>


    <!-- Modal -->
    <div class="modal fade" id="fotoModal" tabindex="-1" role="dialog" aria-labelledby="fotoModalLabel" aria-hidden="true">
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
        $('#fotoModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var foto = button.data('foto');
            var modal = $(this);
            modal.find('.modal-body #modalFoto').attr('src', foto);
        });
    </script>
</body>

</html>
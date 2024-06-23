<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

// Handling form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $nama = $_POST['nama'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $jabatan = $_POST['jabatan'];
        $umur = $_POST['umur'];
        $gaji = $_POST['gaji'];

        $stmt = $pdo->prepare("INSERT INTO pegawai (nama, jenis_kelamin, jabatan, umur, gaji) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama, $jenis_kelamin, $jabatan, $umur, $gaji]);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $jabatan = $_POST['jabatan'];
        $umur = $_POST['umur'];
        $gaji = $_POST['gaji'];

        $stmt = $pdo->prepare("UPDATE pegawai SET nama = ?, jenis_kelamin = ?, jabatan = ?, umur = ?, gaji = ? WHERE id = ?");
        $stmt->execute([$nama, $jenis_kelamin, $jabatan, $umur, $gaji, $id]);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM pegawai WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Fetching data for Chart.js
$maleCount = $pdo->query("SELECT COUNT(*) FROM pegawai WHERE jenis_kelamin='L'")->fetchColumn();
$femaleCount = $pdo->query("SELECT COUNT(*) FROM pegawai WHERE jenis_kelamin='P'")->fetchColumn();

// Fetching employee data
$stmt = $pdo->query("SELECT * FROM pegawai");
$employees = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .navbar-dark {
            background-color: #343a40;
        }
        .navbar-brand img {
            height: 40px;
        }
        .btn-logout {
            background-color: red;
            border: none;
        }
        .btn-logout:hover {
            background-color: darkred;
        }
        .content {
            margin-top: 20px;
        }
        .chart-container {
            width: 50%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">
        <img src="img/logo.png" alt="Logo">
        Jokotole Store
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="report.php">Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Settings</a>
            </li>
            <li class="nav-item">
                <button class="btn btn-logout my-2 my-sm-0" type="button" id="logoutButton">Logout</button>
            </li>
        </ul>
    </div>
</nav>
<div class="container content">
    <h1 class="mt-5">Dashboard</h1>

    <!-- Form to Add/Edit Employee -->
    <form method="POST" action="dashboard.php" class="mt-4">
        <input type="hidden" name="id" id="id">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="form-group col-md-6">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="jabatan">Jabatan</label>
                <input type="text" class="form-control" id="jabatan" name="jabatan" required>
            </div>
            <div class="form-group col-md-6">
                <label for="umur">Umur</label>
                <input type="number" class="form-control" id="umur" name="umur" required>
            </div>
        </div>
        <div class="form-group">
            <label for="gaji">Gaji</label>
            <input type="number" class="form-control" id="gaji" name="gaji" required>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Add</button>
        <button type="submit" name="update" class="btn btn-warning">Update</button>
    </form>

    <!-- Table of Employees -->
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Jabatan</th>
                <th>Umur</th>
                <th>Gaji</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php echo $employee['id']; ?></td>
                    <td><?php echo $employee['nama']; ?></td>
                    <td><?php echo $employee['jenis_kelamin']; ?></td>
                    <td><?php echo $employee['jabatan']; ?></td>
                    <td><?php echo $employee['umur']; ?></td>
                    <td><?php echo $employee['gaji']; ?></td>
                    <td>
                        <button class="btn btn-info btn-sm edit-btn" data-id="<?php echo $employee['id']; ?>" data-nama="<?php echo $employee['nama']; ?>" data-jenis_kelamin="<?php echo $employee['jenis_kelamin']; ?>" data-jabatan="<?php echo $employee['jabatan']; ?>" data-umur="<?php echo $employee['umur']; ?>" data-gaji="<?php echo $employee['gaji']; ?>">Edit</button>
                        <form method="POST" action="dashboard.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Chart.js -->
    <div class="chart-container mt-4">
        <canvas id="genderChart"></canvas>
    </div>
    <script>
        var ctx = document.getElementById('genderChart').getContext('2d');
        var genderChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [<?php echo $maleCount; ?>, <?php echo $femaleCount; ?>],
                    backgroundColor: ['#36A2EB', '#FF6384'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1
            }
        });

        // Script untuk menampilkan konfirmasi sebelum menghapus
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    this.closest('form').submit();
                }
            });
        });

        // Script untuk menampilkan konfirmasi sebelum mengedit
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin mengedit data ini?')) {
                    document.getElementById('id').value = this.dataset.id;
                    document.getElementById('nama').value = this.dataset.nama;
                    document.getElementById('jenis_kelamin').value = this.dataset.jenis_kelamin;
                    document.getElementById('jabatan').value = this.dataset.jabatan;
                    document.getElementById('umur').value = this.dataset.umur;
                    document.getElementById('gaji').value = this.dataset.gaji;
                }
            });
        });

        // Script untuk menampilkan konfirmasi sebelum logout
        document.getElementById('logoutButton').addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = 'logout.php';
            }
        });
    </script>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

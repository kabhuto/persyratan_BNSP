<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'config.php';

// Fetching employee data
$stmt = $pdo->query("SELECT * FROM pegawai");
$employees = $stmt->fetchAll();

// Calculating total salary expenditure
$totalGaji = $pdo->query("SELECT SUM(gaji) FROM pegawai")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item active">
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
    <h1 class="mt-5">Reports</h1>

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
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="alert alert-info mt-4" role="alert">
        <strong>Total Pengeluaran untuk Gaji Karyawan: </strong> Rp <?php echo number_format($totalGaji, 0, ',', '.'); ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Script untuk menampilkan konfirmasi sebelum logout
    document.getElementById('logoutButton').addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = 'logout.php';
        }
    });
</script>
</body>
</html>

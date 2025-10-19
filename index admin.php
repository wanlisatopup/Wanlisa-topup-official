<?php
session_start();
require 'config/auth.php'; // Cek login

// Contoh data dummy (nanti ambil dari database)
$stats = [
    'transactions' => 142,
    'success' => 138,
    'pending' => 3,
    'failed' => 1,
    'revenue' => 'Rp 2.450.000'
];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Admin - TopUpPro</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-content">
    <header>
      <h1>Dashboard</h1>
      <a href="logout.php" class="btn-logout">Logout</a>
    </header>

    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Transaksi</h3>
        <p class="value"><?= $stats['transactions'] ?></p>
      </div>
      <div class="stat-card success">
        <h3>Sukses</h3>
        <p class="value"><?= $stats['success'] ?></p>
      </div>
      <div class="stat-card pending">
        <h3>Pending</h3>
        <p class="value"><?= $stats['pending'] ?></p>
      </div>
      <div class="stat-card failed">
        <h3>Gagal</h3>
        <p class="value"><?= $stats['failed'] ?></p>
      </div>
      <div class="stat-card revenue">
        <h3>Pendapatan</h3>
        <p class="value"><?= $stats['revenue'] ?></p>
      </div>
    </div>

    <h2>Transaksi Terbaru</h2>
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Game</th>
          <th>ID Akun</th>
          <th>Nominal</th>
          <th>Status</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>#TXN1001</td>
          <td>Mobile Legends</td>
          <td>123456789</td>
          <td>100 Diamond</td>
          <td><span class="status success">Sukses</span></td>
          <td>2025-04-05 14:30</td>
        </tr>
        <!-- Tambahkan data dari database -->
      </tbody>
    </table>
  </div>
</body>
</html>
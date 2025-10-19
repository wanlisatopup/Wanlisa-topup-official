<?php
session_start();
require 'config/auth.php';
require 'config/db.php';

$message = '';

// Tambah produk
if ($_POST && isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $code = $conn->real_escape_string($_POST['code']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = (int)$_POST['price'];
    $api_price = (int)$_POST['api_price'];

    $sql = "INSERT INTO products (name, code, category, price, api_price) 
            VALUES ('$name', '$code', '$category', $price, $api_price)";
    
    if ($conn->query($sql)) {
        $message = '<div class="alert success">Produk berhasil ditambahkan!</div>';
    } else {
        $message = '<div class="alert error">Error: ' . $conn->error . '</div>';
    }
}

// Hapus produk
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: products.php');
    exit;
}

// Ambil semua produk
$result = $conn->query("SELECT * FROM products ORDER BY category, name");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Kelola Produk - TopUpPro</title>
  <style>
    <?php include 'assets/css/admin.css'; ?>
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
    .btn-add { background: #00c853; }
    .btn-delete { background: #ff3366; padding: 4px 8px; font-size: 12px; }
    .product-card {
      background: rgba(20, 20, 40, 0.7);
      border: 1px solid #2a2a5a;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
    }
    .product-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .product-info { color: #a0a0d0; font-size: 14px; }
  </style>
</head>
<body>
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-content">
    <header>
      <h1>Kelola Produk</h1>
      <a href="logout.php" class="btn-logout">Logout</a>
    </header>

    <?= $message ?>

    <!-- Form Tambah Produk -->
    <div class="card">
      <h3>Tambah Produk Baru</h3>
      <form method="POST">
        <div class="form-grid">
          <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="name" placeholder="Contoh: MLBB 100 Diamond" required>
          </div>
          <div class="form-group">
            <label>Kode Produk (API)</label>
            <input type="text" name="code" placeholder="Contoh: mlbb-100" required>
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="category" required>
              <option value="game">Game</option>
              <option value="pulsa">Pulsa</option>
              <option value="voucher">Voucher</option>
              <option value="e-wallet">E-Wallet</option>
            </select>
          </div>
          <div class="form-group">
            <label>Harga Jual (Rp)</label>
            <input type="number" name="price" placeholder="12000" required>
          </div>
          <div class="form-group">
            <label>Harga API (Rp)</label>
            <input type="number" name="api_price" placeholder="10000">
          </div>
        </div>
        <button type="submit" name="add_product" class="btn-primary btn-add">+ Tambah Produk</button>
      </form>
    </div>

    <!-- Daftar Produk -->
    <h3>Daftar Produk (<?= $result->num_rows ?>)</h3>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="product-card">
        <div class="product-header">
          <div>
            <strong><?= htmlspecialchars($row['name']) ?></strong>
            <span class="status <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span>
          </div>
          <a href="?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Hapus produk ini?')">Hapus</a>
        </div>
        <div class="product-info">
          Kode: <code><?= htmlspecialchars($row['code']) ?></code> | 
          Kategori: <?= htmlspecialchars($row['category']) ?> | 
          Harga: Rp <?= number_format($row['price'], 0, ',', '.') ?>
          <?php if ($row['api_price'] > 0): ?>
            | API: Rp <?= number_format($row['api_price'], 0, ',', '.') ?>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</body>
</html>


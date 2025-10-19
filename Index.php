<?php
session_start();

// === KONFIGURASI DATABASE — GANTI SESUAI HOSTING ===
$DB_HOST = 'localhost';
$DB_USER = 'root';       // Ganti dengan username DB kamu
$DB_PASS = '';           // Ganti dengan password DB kamu
$DB_NAME = 'topuppro';   // Ganti dengan nama database kamu

// === PASSWORD ADMIN — GANTI! ===
$ADMIN_USER = 'admin';
$ADMIN_PASS_HASH = password_hash('wanlisatopup', PASSWORD_DEFAULT); // Ganti 'password123' jadi password kuat!

// Koneksi Database
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("Error koneksi database: " . $conn->connect_error);
}

// Buat tabel jika belum ada
$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL,
    price INT NOT NULL,
    api_price INT NOT NULL DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active'
)");

$conn->query("CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(50),
    user_id VARCHAR(50),
    zone_id VARCHAR(20),
    amount INT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// === PROTEKSI LOGIN ===
if (!isset($_SESSION['admin_logged'])) {
    if ($_POST && $_POST['action'] == 'login') {
        if ($_POST['username'] === $ADMIN_USER && password_verify($_POST['password'], $ADMIN_PASS_HASH)) {
            $_SESSION['admin_logged'] = true;
            header('Location: admin.php');
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <title>Login Admin - TopUpPro</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <style>
        body { background: #0a0a16; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #0f0f23; border: 1px solid #2a2a5a; border-radius: 12px; padding: 30px; width: 350px; box-shadow: 0 0 20px rgba(0, 119, 255, 0.3); }
        .login-box h2 { color: #00f0ff; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group input { width: 100%; padding: 12px; background: #1a1a2e; border: 1px solid #2a2a5a; border-radius: 6px; color: white; }
        .btn { width: 100%; padding: 12px; background: linear-gradient(90deg, #0077ff, #0055cc); color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .error { color: #ff5555; text-align: center; margin-top: 10px; }
      </style>
    </head>
    <body>
      <div class="login-box">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
          <input type="hidden" name="action" value="login">
          <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
          </div>
          <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <button type="submit" class="btn">Masuk</button>
        </form>
      </div>
    </body>
    </html>
    <?php
    exit;
}

// === LOGOUT ===
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// === SIMPAN PENGATURAN API ===
if ($_POST && $_POST['action'] == 'save_api') {
    $api_url = $_POST['api_url'] ?? '';
    $api_key = $_POST['api_key'] ?? '';
    $header_name = $_POST['header_name'] ?? 'X-API-Key';
    
    $config = "<?php\n";
    $config .= "define('API_URL', '" . addslashes($api_url) . "');\n";
    $config .= "define('API_KEY', '" . addslashes($api_key) . "');\n";
    $config .= "define('API_HEADER', '" . addslashes($header_name) . "');\n";
    $config .= "?>";
    
    file_put_contents('api_config.php', $config);
    $message = "<div style='color:#00f0ff; text-align:center; margin:10px 0;'>✅ Pengaturan API disimpan!</div>";
}

// === TAMBAH PRODUK ===
if ($_POST && $_POST['action'] == 'add_product') {
    $name = $conn->real_escape_string($_POST['name']);
    $code = $conn->real_escape_string($_POST['code']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = (int)$_POST['price'];
    $api_price = (int)$_POST['api_price'];
    
    $conn->query("INSERT INTO products (name, code, category, price, api_price) 
                  VALUES ('$name', '$code', '$category', $price, $api_price)");
    $message = "<div style='color:#00f0ff; text-align:center; margin:10px 0;'>✅ Produk ditambahkan!</div>";
}

// === HAPUS PRODUK ===
if (isset($_GET['delete_product'])) {
    $id = (int)$_GET['delete_product'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: admin.php?page=products');
    exit;
}

// === AMBIL DATA ===
$products = $conn->query("SELECT * FROM products ORDER BY category, name");
$transactions = $conn->query("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 5");

// Cek apakah file api_config.php ada
$api_config = file_exists('api_config.php') ? include 'api_config.php' : false;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel - TopUpPro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
    body { background:#0a0a16; color:#e0e0ff; display:flex; }
    .sidebar { width:220px; background:#0a0a1a; height:100vh; padding:20px 0; border-right:1px solid #1e1e3a; }
    .sidebar .logo { padding:0 20px 20px; color:#00f0ff; font-weight:bold; font-size:20px; }
    .sidebar a { display:block; color:#a0a0d0; padding:12px 20px; text-decoration:none; transition:all 0.3s; }
    .sidebar a:hover, .sidebar a.active { color:#00f0ff; background:rgba(0,119,255,0.1); }
    .main { flex:1; padding:20px; }
    .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
    .btn-logout { background:#ff3366; color:white; padding:8px 16px; text-decoration:none; border-radius:6px; }
    .card { background:rgba(20,20,40,0.7); border:1px solid #2a2a5a; border-radius:10px; padding:20px; margin-bottom:25px; }
    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin-bottom:25px; }
    .stat { background:rgba(0,119,255,0.2); border-left:4px solid #0077ff; padding:15px; border-radius:8px; }
    .stat h3 { font-size:14px; color:#a0a0d0; }
    .stat .value { font-size:24px; font-weight:bold; margin-top:5px; color:#00f0ff; }
    table { width:100%; border-collapse:collapse; background:rgba(20,20,40,0.7); border-radius:8px; overflow:hidden; }
    th, td { padding:12px 15px; text-align:left; border-bottom:1px solid #2a2a5a; }
    .status { padding:4px 10px; border-radius:20px; font-size:12px; }
    .status.success { background:rgba(0,240,255,0.2); color:#00f0ff; }
    .form-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:15px; }
    .form-group { margin-bottom:15px; }
    .form-group label { display:block; margin-bottom:5px; color:#b0b0e0; }
    .form-group input, .form-group select { width:100%; padding:10px; background:#1a1a2e; border:1px solid #2a2a5a; border-radius:6px; color:white; }
    .btn { padding:10px 20px; background:linear-gradient(90deg,#0077ff,#0055cc); color:white; border:none; border-radius:6px; cursor:pointer; }
    .btn-danger { background:#ff3366; }
    .product-item { background:rgba(30,30,50,0.6); padding:15px; border-radius:8px; margin-bottom:15px; }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="logo">TOPUP<span style="color:#4a6cf7">PRO</span></div>
  <a href="admin.php" class="<?= !isset($_GET['page']) ? 'active' : '' ?>">📊 Dashboard</a>
  <a href="admin.php?page=products" class="<?= ($_GET['page'] ?? '') == 'products' ? 'active' : '' ?>">🎮 Produk</a>
  <a href="admin.php?page=settings" class="<?= ($_GET['page'] ?? '') == 'settings' ? 'active' : '' ?>">⚙️ Pengaturan</a>
  <a href="admin.php?logout" class="btn-logout" style="margin-top:20px; display:block; text-align:center;">Logout</a>
</div>

<div class="main">
  <div class="header">
    <h1>
      <?php 
        $page = $_GET['page'] ?? 'dashboard';
        echo match($page) {
          'products' => 'Kelola Produk',
          'settings' => 'Pengaturan API',
          default => 'Dashboard Admin'
        };
      ?>
    </h1>
  </div>

  <?php if (!empty($message)) echo $message; ?>

  <?php if ($page == 'dashboard'): ?>
    <!-- Dashboard -->
    <div class="grid">
      <div class="stat">
        <h3>Total Produk</h3>
        <div class="value"><?= $products->num_rows ?></div>
      </div>
      <div class="stat">
        <h3>Transaksi Hari Ini</h3>
        <div class="value">0</div>
      </div>
    </div>

    <div class="card">
      <h3>Transaksi Terbaru</h3>
      <table>
        <thead><tr><th>ID</th><th>Produk</th><th>Status</th><th>Tanggal</th></tr></thead>
        <tbody>
          <?php while ($t = $transactions->fetch_assoc()): ?>
          <tr>
            <td>#<?= $t['id'] ?></td>
            <td><?= htmlspecialchars($t['product_code']) ?></td>
            <td><span class="status success"><?= ucfirst($t['status']) ?></span></td>
            <td><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  <?php elseif ($page == 'products'): ?>
    <!-- Kelola Produk -->
    <div class="card">
      <h3>Tambah Produk Baru</h3>
      <form method="POST">
        <input type="hidden" name="action" value="add_product">
        <div class="form-grid">
          <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="name" placeholder="Contoh: MLBB 100 Diamond" required>
          </div>
          <div class="form-group">
            <label>Kode Produk (API)</label>
            <input type="text" name="code" placeholder="mlbb-100" required>
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <select name="category" required>
              <option value="game">Game</option>
              <option value="pulsa">Pulsa</option>
              <option value="voucher">Voucher</option>
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
        <button type="submit" class="btn">+ Tambah Produk</button>
      </form>
    </div>

    <div class="card">
      <h3>Daftar Produk (<?= $products->num_rows ?>)</h3>
      <?php while ($p = $products->fetch_assoc()): ?>
        <div class="product-item">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <strong><?= htmlspecialchars($p['name']) ?></strong>
              <div style="font-size:13px; color:#a0a0d0; margin-top:5px;">
                Kode: <code><?= htmlspecialchars($p['code']) ?></code> | 
                Harga: Rp <?= number_format($p['price'],0,',','.') ?>
              </div>
            </div>
            <a href="?page=products&delete_product=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

  <?php elseif ($page == 'settings'): ?>
    <!-- Pengaturan API -->
    <div class="card">
      <h3>Konfigurasi API Top-Up</h3>
      <form method="POST">
        <input type="hidden" name="action" value="save_api">
        <div class="form-group">
          <label>URL API</label>
          <input type="text" name="api_url" value="<?= defined('API_URL') ? API_URL : '' ?>" placeholder="https://api.miraclegaming.store/service">
        </div>
        <div class="form-group">
          <label>API Key</label>
          <input type="text" name="api_key" value="<?= defined('API_KEY') ? API_KEY : '' ?>" placeholder="43d51afe1f86b4d6219d823b">
        </div>
        <div class="form-group">
          <label>Nama Header</label>
          <input type="text" name="header_name" value="<?= defined('API_HEADER') ? API_HEADER : 'X-API-Key' ?>" placeholder="X-API-Key">
        </div>
        <button type="submit" class="btn">💾 Simpan Konfigurasi</button>
      </form>
    </div>

    <div class="card">
      <h3>Catatan Penting</h3>
      <p style="color:#a0a0d0;">
        • File konfigurasi disimpan di: <code>api_config.php</code><br>
        • Pastikan file ini tidak bisa diakses publik!<br>
        • Ganti password admin di kode PHP bagian atas.
      </p>
    </div>
  <?php endif; ?>
</div>

</body>
</html>

<style>
  /* CSS Sidebar (sederhana) */
  body { display: flex; }
  .sidebar {
    width: 220px;
    background: #0a0a1a;
    height: 100vh;
    padding: 20px 0;
    border-right: 1px solid #1e1e3a;
  }
  .sidebar a {
    display: block;
    color: #a0a0d0;
    padding: 12px 20px;
    text-decoration: none;
    transition: all 0.3s;
  }
  .sidebar a:hover, .sidebar a.active {
    color: #00f0ff;
    background: rgba(0, 119, 255, 0.1);
  }
  .main-content {
    flex: 1;
    padding: 20px;
  }
  /* ... tambahkan CSS lain di file terpisah */
</style>

<div class="sidebar">
  <div style="padding: 0 20px 20px; color: #00f0ff; font-weight: bold;">TOPUP<span style="color:#4a6cf7">PRO</span></div>
  <a href="index.php" class="active">📊 Dashboard</a>
  <a href="transactions.php">📋 Transaksi</a>
  <a href="users.php">👥 Pengguna</a>
  <a href="settings.php">⚙️ Pengaturan</a>
</div>


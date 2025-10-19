<?php
session_start();
require 'config/auth.php';

// Simpan konfigurasi di file (atau database)
$config_file = '../config/api_config.php';

if ($_POST) {
    $api_url = $_POST['api_url'] ?? '';
    $api_key = $_POST['api_key'] ?? '';
    $header_name = $_POST['header_name'] ?? 'X-API-Key';

    $config_content = "<?php\n";
    $config_content .= "define('API_URL', '" . addslashes($api_url) . "');\n";
    $config_content .= "define('API_KEY', '" . addslashes($api_key) . "');\n";
    $config_content .= "define('API_HEADER', '" . addslashes($header_name) . "');\n";
    $config_content .= "?>";

    file_put_contents($config_file, $config_content);
    $message = "Konfigurasi berhasil disimpan!";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Pengaturan API - TopUpPro</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <?php include 'includes/sidebar.php'; ?>

  <div class="main-content">
    <header>
      <h1>Pengaturan API</h1>
      <a href="logout.php" class="btn-logout">Logout</a>
    </header>

    <?php if (!empty($message)): ?>
      <div class="alert success"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="settings-form">
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
      <button type="submit" class="btn-primary">Simpan Konfigurasi</button>
    </form>
  </div>
</body>
</html>


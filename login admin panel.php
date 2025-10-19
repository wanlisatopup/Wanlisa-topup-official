<?php
session_start();
if (isset($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // GANTI DENGAN USERNAME & PASSWORD ADMIN KAMU
    if ($username === 'admin' && password_verify($password, '$2y$10$abc123...')) {
        $_SESSION['admin_logged'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login Admin - TopUpPro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { 
      background: #0a0a16; 
      font-family: 'Segoe UI', sans-serif; 
      display: flex; 
      justify-content: center; 
      align-items: center; 
      height: 100vh; 
      margin: 0; 
    }
    .login-box {
      background: #0f0f23;
      border: 1px solid #2a2a5a;
      border-radius: 12px;
      padding: 30px;
      width: 350px;
      box-shadow: 0 0 20px rgba(0, 119, 255, 0.3);
    }
    .login-box h2 {
      color: #00f0ff;
      text-align: center;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group input {
      width: 100%;
      padding: 12px;
      background: #1a1a2e;
      border: 1px solid #2a2a5a;
      border-radius: 6px;
      color: white;
    }
    .btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, #0077ff, #0055cc);
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .error { color: #ff5555; text-align: center; margin-top: 10px; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
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


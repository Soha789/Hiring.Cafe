<?php
session_start();
if (!isset($_SESSION['users'])) $_SESSION['users'] = [];
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = strtolower(trim($_POST['email'] ?? ''));
  $pass = $_POST['password'] ?? '';
  if (isset($_SESSION['users'][$email]) && password_verify($pass, $_SESSION['users'][$email]['password'])) {
    $_SESSION['auth'] = $email;
    echo "<script>alert('Login successful!'); window.location.href='home.php';</script>"; exit;
  } else {
    $msg = 'Invalid email or password.';
  }
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — Hiring.Café</title>
<style>
body{margin:0; font-family:Inter,system-ui; background:#0f1224; color:#e6e8ff}
.wrap{max-width:520px; margin:60px auto; padding:0 16px}
.card{background:linear-gradient(180deg,#15193b,#0e1130); border:1px solid #2a3170; border-radius:20px; padding:24px; box-shadow:0 10px 30px #0009}
input{width:100%; padding:12px 14px; background:#0b0f2a; border:1px solid #2a3170; border-radius:12px; color:#e6e8ff}
button{border:0; padding:12px 16px; border-radius:12px; background:linear-gradient(135deg,#7c5cff,#a78bfa); color:#0b0b0f; font-weight:700; cursor:pointer; width:100%}
a{color:#16e0bd; text-decoration:none}
.notice{color:#ffcf66; margin-bottom:10px}
</style>
</head><body>
<div class="wrap">
  <div class="card">
    <h1>Welcome back</h1>
    <?php if($msg): ?><div class="notice"><?=htmlspecialchars($msg)?></div><?php endif; ?>
    <form method="post">
      <label>Email<br><input type="email" name="email" required></label><br><br>
      <label>Password<br><input type="password" name="password" required></label><br><br>
      <button type="submit">Login</button>
    </form>
    <p style="margin-top:12px"><a href="signup.php">Create an account</a></p>
  </div>
</div>
</body></html>

<?php
session_start();
if (!isset($_SESSION['users'])) $_SESSION['users'] = [];
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $pass = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? 'candidate';
  if (!$name || !$email || !$pass) {
    $msg = 'Please fill all fields.';
  } elseif (isset($_SESSION['users'][$email])) {
    $msg = 'Account already exists. Please log in.';
  } else {
    $_SESSION['users'][$email] = [
      'name'=>$name,'email'=>$email,'password'=>password_hash($pass, PASSWORD_DEFAULT),
      'role'=>$role,'bio'=>'','skills'=>[],'video'=>'','location'=>'','earnings'=>0
    ];
    $_SESSION['earnings'][$email] = 0;
    $_SESSION['auth'] = $email;
    echo "<script>alert('Welcome, ".htmlspecialchars($name)."!');window.location.href='home.php';</script>"; exit;
  }
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Account — Hiring.Café</title>
<style>
body{margin:0; font-family:Inter,system-ui; background:#0f1224; color:#e6e8ff}
.wrap{max-width:980px; margin:40px auto; padding:0 16px}
.card{background:linear-gradient(180deg,#15193b,#0e1130); border:1px solid #2a3170; border-radius:20px; padding:24px; box-shadow:0 10px 30px #0009}
h1{margin:0 0 14px}
.grid{display:grid; gap:14px; grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
input,select,textarea{width:100%; padding:12px 14px; background:#0b0f2a; border:1px solid #2a3170; border-radius:12px; color:#e6e8ff}
button{border:0; padding:12px 16px; border-radius:12px; background:linear-gradient(135deg,#7c5cff,#a78bfa); color:#0b0b0f; font-weight:700; cursor:pointer}
a{color:#16e0bd; text-decoration:none}
.notice{color:#ffcf66; margin-bottom:10px}
</style>
</head><body>
<div class="wrap">
  <div class="card">
    <h1>Join Hiring.Café</h1>
    <p style="color:#cfd4ffaa">Create your account to post jobs or get hired fast.</p>
    <?php if($msg): ?><div class="notice"><?=htmlspecialchars($msg)?></div><?php endif; ?>
    <form method="post" onsubmit="return validate();">
      <div class="grid">
        <div><label>Name<br><input name="name" required></label></div>
        <div><label>Email<br><input type="email" name="email" required></label></div>
        <div><label>Password<br><input type="password" name="password" required minlength="4"></label></div>
        <div><label>Role<br>
          <select name="role">
            <option value="candidate">Candidate</option>
            <option value="recruiter">Recruiter</option>
          </select></label>
        </div>
      </div>
      <div style="margin-top:14px; display:flex; gap:10px; align-items:center">
        <button type="submit">Create Account</button>
        <a href="login.php">Already have an account? Login</a>
      </div>
    </form>
  </div>
</div>
<script>
function validate(){ return true; }
</script>
</body></html>

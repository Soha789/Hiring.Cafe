<?php
session_start();
$me = $_SESSION['auth'] ?? null;
if (!$me){ echo "<script>alert('Please login first.'); window.location.href='login.php';</script>"; exit; }
$user = $_SESSION['users'][$me] ?? null;

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $u =& $_SESSION['users'][$me];
  $u['name'] = trim($_POST['name'] ?? $u['name']);
  $u['bio'] = trim($_POST['bio'] ?? '');
  $u['location'] = trim($_POST['location'] ?? '');
  $u['video'] = trim($_POST['video'] ?? '');
  $skills = array_filter(array_map('trim', explode(',', $_POST['skills'] ?? '')));
  $u['skills'] = $skills;
  echo "<script>alert('Profile updated!'); window.location.href='profile.php';</script>"; exit;
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile — Hiring.Café</title>
<style>
body{margin:0; font-family:Inter,system-ui; background:#0f1224; color:#e6e8ff}
.wrap{max-width:900px; margin:28px auto; padding:0 16px}
.card{background:linear-gradient(180deg,#15193b,#0e1130); border:1px solid #2a3170; border-radius:18px; padding:20px}
.grid{display:grid; gap:12px; grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
input,textarea{width:100%; padding:12px 14px; background:#0b0f2a; border:1px solid #2a3170; border-radius:12px; color:#e6e8ff}
textarea{min-height:120px}
.btn{border:0; padding:12px 16px; border-radius:12px; background:linear-gradient(135deg,#7c5cff,#a78bfa); color:#0b0b0f; font-weight:700; cursor:pointer}
.tag{display:inline-block; padding:6px 10px; background:#222758; border-radius:999px; margin:3px; font-size:12px}
.header{display:flex; justify-content:space-between; align-items:center; gap:8px}
a{color:#16e0bd; text-decoration:none}
</style>
</head><body>
<div class="wrap">
  <div class="card">
    <div class="header">
      <h2 style="margin:0">Your Profile</h2>
      <div>
        <button class="btn" onclick="go('home.php')">Jobs</button>
        <button class="btn" onclick="go('dashboard.php')">Dashboard</button>
        <button class="btn" onclick="go('logout.php')">Logout</button>
      </div>
    </div>
    <p style="color:#cfd4ff99">Make your profile shine—add a short bio, key skills, and a video intro.</p>
    <form method="post">
      <div class="grid">
        <div><label>Name<br><input name="name" value="<?=htmlspecialchars($user['name'])?>"></label></div>
        <div><label>Location<br><input name="location" value="<?=htmlspecialchars($user['location']??'')?>"></label></div>
        <div style="grid-column:1/-1"><label>Bio<br><textarea name="bio" placeholder="Tell companies about your superpowers..."><?=htmlspecialchars($user['bio']??'')?></textarea></label></div>
        <div style="grid-column:1/-1"><label>Skills (comma-separated)<br><input name="skills" value="<?=htmlspecialchars(implode(', ', $user['skills']??[]))?>"></label></div>
        <div style="grid-column:1/-1"><label>Video Intro URL (YouTube/Drive/Meet)<br><input name="video" value="<?=htmlspecialchars($user['video']??'')?>"></label></div>
      </div>
      <div style="margin-top:12px; display:flex; gap:10px; align-items:center">
        <button class="btn">Save Changes</button>
        <a href="<?=htmlspecialchars($user['video']??'#')?>" target="_blank">Preview Video</a>
      </div>
    </form>
    <div style="margin-top:14px">
      <?php foreach(($user['skills']??[]) as $s): ?><span class="tag">#<?=htmlspecialchars($s)?></span><?php endforeach; ?>
    </div>
  </div>
</div>
<script>
function go(p){ window.location.href=p; }
</script>
</body></html>

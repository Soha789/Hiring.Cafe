<?php
session_start();
if (!isset($_SESSION['users'])) $_SESSION['users'] = [];
if (!isset($_SESSION['jobs'])) $_SESSION['jobs'] = [];
$me = $_SESSION['auth'] ?? null;
$user = $me ? ($_SESSION['users'][$me] ?? null) : null;

$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!$me || ($user['role']??'')!=='recruiter') {
    echo "<script>alert('Only recruiters can add jobs.'); window.location.href='login.php';</script>"; exit;
  }
  $id = (count($_SESSION['jobs'])? max(array_keys($_SESSION['jobs'])):0)+1;
  $skills = array_filter(array_map('trim', explode(',', $_POST['skills'] ?? '')));
  $_SESSION['jobs'][$id] = [
    'id'=>$id,
    'title'=>trim($_POST['title']??''),
    'company'=>trim($_POST['company']??$user['name']??'Company'),
    'location'=>trim($_POST['location']??'Remote'),
    'type'=>trim($_POST['type']??'Full-time'),
    'salary'=>trim($_POST['salary']??'Negotiable'),
    'skills'=>$skills ?: ['Communication'],
    'desc'=>trim($_POST['desc']??''),
    'interview'=>trim($_POST['interview']??''),
    'reward'=>floatval($_POST['reward']??0)
  ];
  echo "<script>alert('Job added successfully!'); window.location.href='home.php';</script>"; exit;
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Job — Hiring.Café</title>
<style>
body{margin:0; font-family:Inter,system-ui; background:#0f1224; color:#e6e8ff}
.wrap{max-width:900px; margin:30px auto; padding:0 16px}
.card{background:linear-gradient(180deg,#15193b,#0e1130); border:1px solid #2a3170; border-radius:18px; padding:20px}
input,select,textarea{width:100%; padding:12px 14px; background:#0b0f2a; border:1px solid #2a3170; border-radius:12px; color:#e6e8ff}
textarea{min-height:120px}
.grid{display:grid; gap:12px; grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}
.btn{border:0; padding:12px 16px; border-radius:12px; background:linear-gradient(135deg,#7c5cff,#a78bfa); color:#0b0b0f; font-weight:700; cursor:pointer}
a{color:#16e0bd; text-decoration:none}
.badge{font-size:12px; color:#cfd4ff99}
</style>
</head><body>
<div class="wrap">
  <div class="card">
    <h2 style="margin:0 0 8px">Post a Job</h2>
    <p class="badge">Only recruiters can post jobs. Include a meeting link for instant interviews.</p>
    <form method="post" onsubmit="return requireRecruiter();">
      <div class="grid">
        <div><label>Job Title<br><input name="title" required></label></div>
        <div><label>Company<br><input name="company" value="<?=htmlspecialchars($user['name']??'')?>"></label></div>
        <div><label>Location<br><input name="location" placeholder="e.g., Remote, Riyadh"></label></div>
        <div><label>Type<br>
          <select name="type">
            <?php foreach(['Full-time','Part-time','Contract','Remote','Hybrid'] as $t): ?>
              <option><?=$t?></option>
            <?php endforeach; ?>
          </select></label>
        </div>
        <div><label>Salary Range<br><input name="salary" placeholder="e.g., $3k–$5k/mo"></label></div>
        <div><label>Reward on Apply (USD)<br><input type="number" step="0.01" name="reward" value="25"></label></div>
        <div style="grid-column:1/-1"><label>Skills (comma-separated)<br><input name="skills" placeholder="React, Node, SQL"></label></div>
        <div style="grid-column:1/-1"><label>Description<br><textarea name="desc" placeholder="What will the hire do?"></textarea></label></div>
        <div style="grid-column:1/-1"><label>Interview Link (Google Meet/Zoom, etc.)<br><input name="interview" placeholder="https://meet.example.com/room"></label></div>
      </div>
      <div style="margin-top:12px; display:flex; gap:10px; align-items:center">
        <button class="btn">Publish Job</button>
        <a href="home.php">Back to Jobs</a>
      </div>
    </form>
  </div>
</div>
<script>
function requireRecruiter(){
  <?php if(!$me){ ?> alert('Login as recruiter to add jobs.'); window.location.href='login.php'; return false; <?php } ?>
  <?php if(($user['role']??'')!=='recruiter'){ ?> alert('Only recruiters can add jobs.'); return false; <?php } ?>
  return true;
}
</script>
</body></html>

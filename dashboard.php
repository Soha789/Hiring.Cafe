<?php
session_start();
$me = $_SESSION['auth'] ?? null;
$user = $me ? ($_SESSION['users'][$me] ?? null) : null;
if (!$me) { echo "<script>alert('Please login first.'); window.location.href='login.php';</script>"; exit; }
$earn = $_SESSION['earnings'][$me] ?? 0;
$apps = array_values(array_filter($_SESSION['applications'] ?? [], fn($a)=>$a['candidate']===$me));
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard — Hiring.Café</title>
<style>
body{margin:0; font-family:Inter,system-ui; background:#0f1224; color:#e6e8ff}
.wrap{max-width:1000px; margin:28px auto; padding:0 16px}
.card{background:linear-gradient(180deg,#15193b,#0e1130); border:1px solid #2a3170; border-radius:18px; padding:20px}
.grid{display:grid; gap:14px; grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
a{color:#16e0bd; text-decoration:none}
.stat{font-size:36px; font-weight:800; color:#16e0bd}
.table{width:100%; border-collapse:collapse; margin-top:8px}
.table th,.table td{padding:10px 8px; border-bottom:1px solid #2a3170; font-size:14px}
.btn{border:0; padding:10px 14px; border-radius:12px; background:linear-gradient(135deg,#7c5cff,#a78bfa); color:#0b0b0f; font-weight:700; cursor:pointer}
</style>
</head><body>
<div class="wrap">
  <div class="grid">
    <div class="card">
      <h2 style="margin:0 0 6px">Earnings</h2>
      <div class="stat">$<?=number_format($earn,2)?></div>
      <p style="color:#cfd4ff99">Earnings grow each time you apply (demo flow using job rewards).</p>
      <div style="display:flex; gap:8px">
        <button class="btn" onclick="go('home.php')">Find More Jobs</button>
        <button class="btn" onclick="go('profile.php')">Improve Profile</button>
      </div>
    </div>
    <div class="card">
      <h2 style="margin:0 0 6px">Profile Snapshot</h2>
      <p><strong><?=htmlspecialchars($user['name'])?></strong> (<?=htmlspecialchars($user['role'])?>)<br><?=htmlspecialchars($user['email'])?></p>
      <p style="color:#cfd4ffaa"><?=nl2br(htmlspecialchars($user['bio'] ?: 'No bio yet.'))?></p>
      <p>
        <?php foreach(($user['skills']?:[]) as $s): ?>
          <span style="display:inline-block; padding:6px 10px; background:#222758; border-radius:999px; margin:3px; font-size:12px">#<?=htmlspecialchars($s)?></span>
        <?php endforeach; ?>
      </p>
      <?php if($user['video']): ?><p>Intro Video: <a href="<?=htmlspecialchars($user['video'])?>" target="_blank">Open</a></p><?php endif; ?>
    </div>
  </div>

  <div class="card" style="margin-top:14px">
    <h2 style="margin:0 0 10px">Your Applications</h2>
    <table class="table">
      <tr><th>Job</th><th>Company</th><th>Applied On</th><th>Interview Link</th><th>Reward</th></tr>
      <?php foreach($apps as $a):
        $j = $_SESSION['jobs'][$a['job_id']] ?? null; if(!$j) continue; ?>
        <tr>
          <td><?=htmlspecialchars($j['title'])?></td>
          <td><?=htmlspecialchars($j['company'])?></td>
          <td><?=date('Y-m-d H:i',$a['ts'])?></td>
          <td><a href="<?=htmlspecialchars($j['interview'])?>" target="_blank">Open</a></td>
          <td>$<?=number_format($j['reward']??0,2)?></td>
        </tr>
      <?php endforeach; if(!$apps): ?>
        <tr><td colspan="5" style="color:#cfd4ff99">No applications yet.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>
<script>
function go(p){ window.location.href=p; }
</script>
</body></html>

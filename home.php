<?php
session_start();
if (!isset($_SESSION['jobs'])) $_SESSION['jobs'] = [];
if (!isset($_SESSION['applications'])) $_SESSION['applications'] = [];
if (!isset($_SESSION['earnings'])) $_SESSION['earnings'] = [];
$me = $_SESSION['auth'] ?? null;
$user = $me ? ($_SESSION['users'][$me] ?? null) : null;

/* Handle Apply action (POST) and redirect via JS */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['apply_job'])) {
  if (!$me) { echo "<script>alert('Please login to apply.'); window.location.href='login.php';</script>"; exit; }
  $jobId = (int)$_POST['job_id'];
  if (isset($_SESSION['jobs'][$jobId])) {
    $_SESSION['applications'][] = ['job_id'=>$jobId,'candidate'=>$me,'ts'=>time()];
    $reward = floatval($_SESSION['jobs'][$jobId]['reward'] ?? 0);
    $_SESSION['earnings'][$me] = ($_SESSION['earnings'][$me] ?? 0) + $reward;
    echo "<script>alert('Applied! Interview link is on the job card.'); window.location.href='home.php';</script>"; exit;
  }
}
$jobs = array_values($_SESSION['jobs']);
$q = strtolower(trim($_GET['q'] ?? ''));
$type = $_GET['type'] ?? '';
$loc = strtolower(trim($_GET['loc'] ?? ''));
if ($q || $type || $loc) {
  $jobs = array_filter($jobs, function($j) use ($q,$type,$loc){
    $hit = true;
    if ($q) $hit = $hit && (str_contains(strtolower($j['title']),$q) || str_contains(strtolower($j['company']),$q) || str_contains(strtolower($j['desc']),$q));
    if ($type) $hit = $hit && ($j['type']===$type);
    if ($loc) $hit = $hit && str_contains(strtolower($j['location']),$loc);
    return $hit;
  });
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Jobs — Hiring.Café</title>
<style>
body{margin:0; font-family:Inter,system-ui; background:#0f1224; color:#e6e8ff}
nav{display:flex; align-items:center; justify-content:space-between; padding:16px 20px; background:#0f1224cc; position:sticky; top:0; backdrop-filter:blur(8px)}
.brand{display:flex; gap:10px; align-items:center}
.brand .dot{width:12px; height:12px; border-radius:50%; background:linear-gradient(135deg,#7c5cff,#16e0bd)}
a{color:#16e0bd; text-decoration:none}
.btn{border:0; padding:10px 14px; border-radius:12px; background:linear-gradient(135deg,#7c5cff,#a78bfa); color:#0b0b0f; font-weight:700; cursor:pointer}
.btn.ghost{background:#ffffff12; color:#e6e8ff; border:1px solid #ffffff24}
.wrap{max-width:1100px; margin:20px auto; padding:0 16px}
.controls{display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:10px; margin:10px 0 18px}
input,select{width:100%; padding:10px 12px; background:#0b0f2a; border:1px solid #2a3170; border-radius:12px; color:#e6e8ff}
.grid{display:grid; gap:14px; grid-template-columns:repeat(auto-fit,minmax(280px,1fr))}
.card{background:linear-gradient(180deg,#15193b,#0e1130); border:1px solid #2a3170; border-radius:18px; padding:16px}
.chip{display:inline-flex; padding:5px 9px; border-radius:999px; background:#222758; color:#cfd4ff; font-size:12px; margin:4px 4px 0 0}
.meta{color:#cfd4ffaa; font-size:13px}
.salary{color:#16e0bd; font-weight:600}
.head{display:flex; justify-content:space-between; gap:8px; align-items:flex-start}
</style>
</head><body>
<nav>
  <div class="brand"><div class="dot"></div><strong>&nbsp;Hiring.Café — Jobs</strong></div>
  <div style="display:flex; gap:8px; align-items:center">
    <button class="btn ghost" onclick="go('index.php')">Home</button>
    <button class="btn ghost" onclick="go('add_jobs.php')">Add Job</button>
    <button class="btn ghost" onclick="go('dashboard.php')">Dashboard</button>
    <button class="btn ghost" onclick="go('profile.php')">Profile</button>
    <?php if($me): ?>
      <span style="font-size:13px;color:#cfd4ff99">Hi, <?=htmlspecialchars($_SESSION['users'][$me]['name']??'')?> (<?=htmlspecialchars($_SESSION['users'][$me]['role']??'')?>)</span>
      <button class="btn" onclick="go('logout.php')">Logout</button>
    <?php else: ?>
      <button class="btn" onclick="go('login.php')">Login</button>
    <?php endif; ?>
  </div>
</nav>

<div class="wrap">
  <form class="controls" method="get">
    <input name="q" placeholder="Search by title, company, or keyword" value="<?=htmlspecialchars($_GET['q']??'')?>">
    <select name="type">
      <option value="">Any type</option>
      <?php foreach(['Full-time','Part-time','Contract','Remote','Hybrid'] as $t): ?>
        <option value="<?=$t?>" <?=(($_GET['type']??'')===$t)?'selected':''?>><?=$t?></option>
      <?php endforeach; ?>
    </select>
    <input name="loc" placeholder="Location (e.g., Riyadh, Remote)" value="<?=htmlspecialchars($_GET['loc']??'')?>">
    <button class="btn">Filter</button>
  </form>

  <div class="grid">
    <?php foreach($jobs as $j): ?>
    <div class="card">
      <div class="head">
        <div>
          <h3 style="margin:0 0 6px"><?=htmlspecialchars($j['title'])?></h3>
          <div class="meta"><?=htmlspecialchars($j['company'])?> • <?=htmlspecialchars($j['location'])?> • <?=htmlspecialchars($j['type'])?></div>
          <div class="meta" style="margin-top:6px">Salary: <span class="salary"><?=htmlspecialchars($j['salary'])?></span> • Interview: <a href="<?=htmlspecialchars($j['interview'])?>" target="_blank">Open link</a></div>
        </div>
        <div class="meta">Reward on apply: <strong class="salary">$<?=number_format($j['reward']??0,2)?></strong></div>
      </div>
      <p style="margin:10px 0 6px; color:#cfd4ff"><?=htmlspecialchars($j['desc'])?></p>
      <div style="margin:8px 0 10px">
        <?php foreach($j['skills'] as $s): ?><span class="chip">#<?=htmlspecialchars($s)?></span><?php endforeach; ?>
      </div>
      <form method="post" onsubmit="return ensureLogin();">
        <input type="hidden" name="job_id" value="<?=$j['id']?>">
        <button class="btn" name="apply_job" value="1">Apply Now</button>
      </form>
    </div>
    <?php endforeach; ?>
    <?php if(!$jobs): ?>
      <div class="card"><em>No jobs found. Try different filters.</em></div>
    <?php endif; ?>
  </div>
</div>

<script>
function go(p){ window.location.href=p; }
function ensureLogin(){
  <?php if(!$me){ ?> alert('Please log in to apply.'); window.location.href='login.php'; return false; <?php } ?>
  return confirm('Apply to this job? Your profile will be shared.');
}
</script>
</body></html>

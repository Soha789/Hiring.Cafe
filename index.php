<?php
session_start();
/* ---------- Bootstrapping mock "DB" in session ---------- */
if (!isset($_SESSION['users'])) $_SESSION['users'] = [];          // [email => [...user]]
if (!isset($_SESSION['jobs'])) $_SESSION['jobs'] = [];            // [jobId => [...job]]
if (!isset($_SESSION['applications'])) $_SESSION['applications'] = []; // [[jobId, candidateEmail, ts]]
if (!isset($_SESSION['earnings'])) $_SESSION['earnings'] = [];    // [email => float]

/* Seed a couple demo jobs once */
if (!isset($_SESSION['seeded'])) {
  $_SESSION['seeded'] = true;
  $id = 1;
  $_SESSION['jobs'][$id++] = [
    'id'=>1,'title'=>'Frontend Engineer','company'=>'SkyPixel',
    'location'=>'Remote','type'=>'Full-time','salary'=>'$3k–$5k/mo',
    'skills'=>['HTML','CSS','JavaScript','React'],
    'desc'=>'Build delightful UIs and ship fast.',
    'interview'=>'https://meet.example.com/skypixel',
    'reward'=>50
  ];
  $_SESSION['jobs'][$id++] = [
    'id'=>2,'title'=>'Data Analyst','company'=>'InsightNest',
    'location'=>'Riyadh','type'=>'Hybrid','salary'=>'SAR 12k–16k',
    'skills'=>['SQL','Python','Excel','Power BI'],
    'desc'=>'Turn data into decisions.',
    'interview'=>'https://meet.example.com/insightnest',
    'reward'=>60
  ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Hiring.Café — Fast Hiring & Skill Showcases</title>
<style>
:root{
  --bg:#0f1224; --card:#15193b; --ink:#e6e8ff; --muted:#a8b0ff; --accent:#7c5cff; --accent2:#16e0bd;
  --chip:#222758; --chip2:#1c7; --warn:#ffb703;
}
*{box-sizing:border-box} body{
  margin:0; font-family:Inter,system-ui,Segoe UI,Arial; color:var(--ink); background:
  radial-gradient(1200px 600px at 90% -10%, #23275633, transparent 60%),
  radial-gradient(900px 600px at -10% 110%, #0ad7bd22, transparent 60%),
  var(--bg);
}
nav{display:flex; align-items:center; justify-content:space-between; padding:18px 28px; position:sticky; top:0; background:linear-gradient(180deg, #0f1224cc, #0f122400); backdrop-filter:blur(8px)}
.brand{display:flex; gap:10px; align-items:center}
.logo{width:36px; height:36px; border-radius:12px; background:
linear-gradient(135deg, var(--accent), var(--accent2)); box-shadow:0 10px 28px #0006}
.brand h1{font-size:18px; margin:0; letter-spacing:.4px}
nav .actions button{margin-left:10px; border:0; padding:10px 14px; border-radius:12px; cursor:pointer; color:#0b0b0f}
.btn{
  background:linear-gradient(135deg, var(--accent), #a78bfa);
  color:#0b0b0f; box-shadow:0 6px 18px #0008; font-weight:600;
}
.btn.ghost{background:#ffffff12; color:var(--ink); border:1px solid #ffffff24}
.hero{display:grid; grid-template-columns:1.2fr .8fr; gap:28px; padding:40px 28px 10px}
.kicker{display:inline-flex; gap:8px; align-items:center; padding:6px 10px; background:#ffffff12; border:1px solid #ffffff24; border-radius:999px; font-size:12px; color:var(--muted)}
h2{font-size:42px; line-height:1.05; margin:16px 0}
.lead{color:#c6cbff; margin:10px 0 20px; max-width:60ch}
.cta{display:flex; gap:12px}
.card{
  background:linear-gradient(180deg, #15193b, #0e1130);
  border:1px solid #2a3170; border-radius:20px; padding:20px; box-shadow:0 10px 30px #0009;
}
.grid{display:grid; gap:16px}
.grid.jobs{grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
.chip{display:inline-flex; padding:6px 10px; border-radius:999px; background:var(--chip); color:#cfd4ff; font-size:12px; margin:4px 4px 0 0}
.job h3{margin:8px 0 6px; font-size:18px}
.meta{font-size:13px; color:#cfd4ffaa}
.salary{color:var(--accent2); font-weight:600}
.footer{padding:32px 28px 60px; color:#cfd4ff99; text-align:center}
a.inline{color:var(--accent2); text-decoration:none; border-bottom:1px dashed #16e0bd66}
.highlight{background:linear-gradient(90deg, #16e0bd22, transparent 60%); border-left:3px solid var(--accent2); padding:10px 12px; border-radius:12px}
</style>
</head>
<body>
<nav>
  <div class="brand">
    <div class="logo"></div>
    <h1>Hiring.Café</h1>
  </div>
  <div class="actions">
    <button class="btn ghost" onclick="go('login.php')">Login</button>
    <button class="btn" onclick="go('signup.php')">Create Account</button>
  </div>
</nav>

<section class="hero">
  <div class="card">
    <div class="kicker">⚡ Fast interviews • 🎯 Skill-focused • ☕ Café-smooth hiring</div>
    <h2>Hire in days, not weeks. Showcase skills, schedule interviews, get hired.</h2>
    <p class="lead">A lightweight platform inspired by <span class="highlight">Hiring.Cafe</span>—designed for speed:
      recruiters post roles with instant interview links, and candidates shine with skill tags and short video intros.</p>
    <div class="cta">
      <button class="btn" onclick="go('signup.php')">Get Started</button>
      <button class="btn ghost" onclick="go('home.php')">Browse Jobs</button>
    </div>
    <p style="margin-top:14px;font-size:14px;color:#cfd4ff99">No long forms. No waiting. Just skills → chat → offer.</p>
  </div>
  <div class="card">
    <h3 style="margin:0 0 10px">Trending Jobs</h3>
    <div class="grid jobs">
      <?php foreach(array_slice(array_values($_SESSION['jobs']),0,4) as $j): ?>
      <div class="card job">
        <h3><?=htmlspecialchars($j['title'])?></h3>
        <div class="meta"><?=htmlspecialchars($j['company'])?> • <?=htmlspecialchars($j['location'])?> • <?=htmlspecialchars($j['type'])?></div>
        <div class="meta" style="margin-top:6px">Salary: <span class="salary"><?=htmlspecialchars($j['salary'])?></span></div>
        <div style="margin:8px 0 6px">
          <?php foreach($j['skills'] as $s): ?>
            <span class="chip">#<?=htmlspecialchars($s)?></span>
          <?php endforeach; ?>
        </div>
        <button class="btn" style="margin-top:10px" onclick="go('home.php')">View & Apply</button>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section style="padding:10px 28px 0">
  <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr))">
    <div class="card">
      <h3>For Candidates</h3>
      <p>Upload a quick video intro, add your strongest skills, and apply with one click. Your <a class="inline" onclick="go('profile.php')">profile</a> becomes your pitch.</p>
    </div>
    <div class="card">
      <h3>For Recruiters</h3>
      <p>Post roles in minutes, add an instant meeting link, and chat directly. Try <a class="inline" onclick="go('add_jobs.php')">adding a job</a>.</p>
    </div>
    <div class="card">
      <h3>Fast Interviews</h3>
      <p>Every job can include a video link for immediate screening—no back-and-forth scheduling.</p>
    </div>
  </div>
</section>

<div class="footer">© <?=date('Y')?> Hiring.Café — Demo clone for learning purposes.</div>

<script>
function go(path){ window.location.href=path; }
</script>
</body>
</html>

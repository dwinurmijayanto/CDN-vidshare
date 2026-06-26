<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Terima Kasih – VIDSHARE.MY.ID</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Sora:wght@300;600;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:       #0a0c12;
      --surface:  #111420;
      --ring:     #1e2235;
      --accent:   #6c63ff;
      --glow:     #a89cff;
      --text:     #e8e6ff;
      --muted:    #7a77a8;
      --white:    #ffffff;
      --danger:   #ff6584;
    }

    html, body {
      height: 100%;
      background: var(--bg);
      color: var(--text);
      font-family: 'Space Grotesk', sans-serif;
      overflow: hidden;
    }

    /* ── Stars ── */
    #stars-canvas {
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 0;
    }

    /* ── Glow orbs ── */
    .orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(100px);
      opacity: .18;
      pointer-events: none;
      z-index: 0;
      animation: drift 14s ease-in-out infinite alternate;
    }
    .orb-a { width: 480px; height: 480px; background: var(--accent); top: -120px; left: -100px; animation-duration: 16s; }
    .orb-b { width: 360px; height: 360px; background: #ff6584; bottom: -80px; right: -80px; animation-duration: 20s; animation-delay: -5s; }
    @keyframes drift { to { transform: translate(40px, 30px); } }

    /* ── Card ── */
    .card {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem 1.25rem;
      text-align: center;
    }

    /* ── Logo badge ── */
    .logo-wrap {
      margin-bottom: 2rem;
      opacity: 0;
      transform: translateY(-30px);
      animation: fadeDown .7s .2s cubic-bezier(.22,1,.36,1) forwards;
    }
    .logo {
      display: inline-flex;
      align-items: center;
      gap: .6rem;
      background: var(--surface);
      border: 1px solid var(--ring);
      border-radius: 999px;
      padding: .45rem 1.1rem;
      font-family: 'Sora', sans-serif;
      font-weight: 800;
      font-size: 1rem;
      letter-spacing: .06em;
      color: var(--white);
    }
    .logo-dot {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: var(--accent);
      box-shadow: 0 0 8px var(--accent);
      animation: pulse-dot 1.6s ease-in-out infinite;
    }
    @keyframes pulse-dot {
      0%,100% { transform: scale(1); opacity: 1; }
      50%      { transform: scale(1.5); opacity: .6; }
    }

    /* ── Heart ── */
    .heart-wrap {
      position: relative;
      margin-bottom: 1.8rem;
      opacity: 0;
      transform: scale(.4);
      animation: popIn .6s .5s cubic-bezier(.34,1.56,.64,1) forwards;
    }
    .heart {
      font-size: clamp(3.5rem, 10vw, 5rem);
      display: block;
      animation: heartbeat 1.4s ease-in-out infinite;
      filter: drop-shadow(0 0 24px #ff6584aa);
    }
    .heart-ring {
      position: absolute;
      inset: -16px;
      border-radius: 50%;
      border: 2px solid #ff658440;
      animation: expand-ring 1.4s ease-out infinite;
    }
    @keyframes heartbeat {
      0%,100% { transform: scale(1); }
      14%     { transform: scale(1.18); }
      28%     { transform: scale(1); }
      42%     { transform: scale(1.1); }
      70%     { transform: scale(1); }
    }
    @keyframes expand-ring {
      0%   { transform: scale(.8); opacity: .7; }
      100% { transform: scale(2);  opacity: 0; }
    }

    /* ── Headline ── */
    .headline {
      font-family: 'Sora', sans-serif;
      font-weight: 800;
      font-size: clamp(1.8rem, 6vw, 3rem);
      line-height: 1.15;
      color: var(--white);
      margin-bottom: .75rem;
      opacity: 0;
      transform: translateY(20px);
      animation: fadeUp .65s .7s cubic-bezier(.22,1,.36,1) forwards;
    }
    .headline span {
      background: linear-gradient(120deg, var(--accent), var(--glow), #ff6584);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* ── Sub ── */
    .sub {
      font-size: clamp(.95rem, 2.5vw, 1.1rem);
      color: var(--muted);
      max-width: 380px;
      line-height: 1.6;
      margin-bottom: 2.4rem;
      opacity: 0;
      transform: translateY(20px);
      animation: fadeUp .65s .9s cubic-bezier(.22,1,.36,1) forwards;
    }

    /* ── Timer ring ── */
    .timer-wrap {
      position: relative;
      width: 120px;
      height: 120px;
      margin-bottom: 2rem;
      opacity: 0;
      animation: fadeUp .65s 1.1s cubic-bezier(.22,1,.36,1) forwards;
    }
    .timer-svg { transform: rotate(-90deg); }
    .timer-track {
      fill: none;
      stroke: var(--ring);
      stroke-width: 6;
    }
    .timer-progress {
      fill: none;
      stroke: url(#grad);
      stroke-width: 6;
      stroke-linecap: round;
      stroke-dasharray: 339.29;
      stroke-dashoffset: 0;
      transition: stroke-dashoffset 1s linear;
    }
    .timer-label {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .timer-number {
      font-family: 'Sora', sans-serif;
      font-size: 2.2rem;
      font-weight: 800;
      color: var(--white);
      line-height: 1;
    }
    .timer-text {
      font-size: .65rem;
      color: var(--muted);
      letter-spacing: .08em;
      text-transform: uppercase;
      margin-top: 3px;
    }

    /* ── Redirect note ── */
    .redirect-note {
      font-size: .85rem;
      color: var(--muted);
      margin-bottom: 1.6rem;
      opacity: 0;
      animation: fadeUp .65s 1.3s cubic-bezier(.22,1,.36,1) forwards;
    }
    .redirect-note a {
      color: var(--glow);
      text-decoration: none;
    }
    .redirect-note a:hover { text-decoration: underline; }

    /* ── Button ── */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      padding: .8rem 1.8rem;
      background: linear-gradient(135deg, var(--accent), #a89cff);
      color: var(--white);
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 700;
      font-size: .95rem;
      border-radius: 999px;
      border: none;
      cursor: pointer;
      text-decoration: none;
      box-shadow: 0 4px 28px #6c63ff55;
      transition: transform .2s, box-shadow .2s;
      opacity: 0;
      animation: fadeUp .65s 1.5s cubic-bezier(.22,1,.36,1) forwards;
    }
    .btn:hover {
      transform: translateY(-3px) scale(1.04);
      box-shadow: 0 8px 36px #6c63ff88;
    }
    .btn svg { width: 16px; height: 16px; }

    /* ── Particles floating ── */
    .particles { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
    .particle {
      position: absolute;
      border-radius: 50%;
      background: var(--accent);
      opacity: 0;
      animation: floatUp linear infinite;
    }

    /* ── Keyframes ── */
    @keyframes fadeDown { to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeUp   { to { opacity: 1; transform: translateY(0); } }
    @keyframes popIn    { to { opacity: 1; transform: scale(1); } }
    @keyframes floatUp  {
      0%   { transform: translateY(0)   scale(1);   opacity: .6; }
      100% { transform: translateY(-90vh) scale(.3); opacity: 0; }
    }

    /* ── Reduced motion ── */
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation-duration: .001ms !important; }
    }
  </style>
</head>
<body>

<?php
  $redirect_url = "https://www.facebook.com/groups/1035141368937312";
  $delay = 5;
?>

<!-- Glow orbs -->
<div class="orb orb-a"></div>
<div class="orb orb-b"></div>

<!-- Stars canvas -->
<canvas id="stars-canvas"></canvas>

<!-- Floating particles -->
<div class="particles" id="particles"></div>

<!-- Main card -->
<div class="card">

  <div class="logo-wrap">
    <div class="logo">
      <span class="logo-dot"></span>
      VIDSHARE.MY.ID
    </div>
  </div>

  <div class="heart-wrap">
    <span class="heart">💜</span>
    <div class="heart-ring"></div>
  </div>

  <h1 class="headline">
    Terima Kasih<br><span>atas Dukunganmu!</span>
  </h1>

  <p class="sub">
    Setiap dukungan kalian adalah semangat kami untuk terus berkembang dan memberikan yang terbaik. Kami sangat menghargai kepercayaan kalian.
  </p>

  <!-- Countdown ring -->
  <div class="timer-wrap">
    <svg class="timer-svg" width="120" height="120" viewBox="0 0 120 120">
      <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#6c63ff"/>
          <stop offset="100%" stop-color="#ff6584"/>
        </linearGradient>
      </defs>
      <circle class="timer-track" cx="60" cy="60" r="54"/>
      <circle class="timer-progress" id="timer-ring" cx="60" cy="60" r="54"/>
    </svg>
    <div class="timer-label">
      <span class="timer-number" id="timer-number"><?= $delay ?></span>
      <span class="timer-text">detik</span>
    </div>
  </div>

  <p class="redirect-note">
    Mengalihkan ke <a href="<?= htmlspecialchars($redirect_url) ?>" target="_blank">Facebook Group</a>&hellip;
  </p>

  <a class="btn" href="<?= htmlspecialchars($redirect_url) ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
      <polyline points="15 3 21 3 21 9"/>
      <line x1="10" y1="14" x2="21" y2="3"/>
    </svg>
    Kunjungi Grup Sekarang
  </a>

</div>

<script>
(function () {

  /* ── Stars ── */
  const canvas = document.getElementById('stars-canvas');
  const ctx    = canvas.getContext('2d');
  let stars = [];

  function resize() {
    canvas.width  = window.innerWidth;
    canvas.height = window.innerHeight;
    initStars();
  }

  function initStars() {
    stars = [];
    const count = Math.floor((canvas.width * canvas.height) / 5000);
    for (let i = 0; i < count; i++) {
      stars.push({
        x:    Math.random() * canvas.width,
        y:    Math.random() * canvas.height,
        r:    Math.random() * 1.5 + .3,
        a:    Math.random(),
        da:   (Math.random() - .5) * .006,
        speed: Math.random() * .15 + .05,
      });
    }
  }

  function drawStars() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (const s of stars) {
      s.a += s.da;
      if (s.a <= 0 || s.a >= 1) s.da *= -1;
      s.y -= s.speed;
      if (s.y < -2) s.y = canvas.height + 2;
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(168,156,255,${s.a})`;
      ctx.fill();
    }
    requestAnimationFrame(drawStars);
  }

  window.addEventListener('resize', resize);
  resize();
  drawStars();

  /* ── Particles ── */
  const particleContainer = document.getElementById('particles');
  function spawnParticle() {
    const p   = document.createElement('div');
    const size = Math.random() * 6 + 3;
    const colors = ['#6c63ff','#a89cff','#ff6584','#ffb3c1','#ffffff'];
    p.className = 'particle';
    p.style.cssText = `
      width:${size}px; height:${size}px;
      left:${Math.random() * 100}%;
      bottom:-10px;
      background:${colors[Math.floor(Math.random()*colors.length)]};
      animation-duration:${Math.random()*6+5}s;
      animation-delay:${Math.random()*3}s;
    `;
    particleContainer.appendChild(p);
    setTimeout(() => p.remove(), 12000);
  }
  setInterval(spawnParticle, 600);

  /* ── Countdown ── */
  const total    = <?= $delay ?>;
  const fullDash = 339.29;
  const ring     = document.getElementById('timer-ring');
  const numEl    = document.getElementById('timer-number');
  let   remaining = total;
  const redirectUrl = <?= json_encode($redirect_url) ?>;

  function tick() {
    remaining--;
    numEl.textContent = remaining;

    const pct = remaining / total;
    ring.style.strokeDashoffset = fullDash * (1 - pct);

    // pulse the number
    numEl.style.transform = 'scale(1.3)';
    setTimeout(() => numEl.style.transform = 'scale(1)', 200);
    numEl.style.transition = 'transform .2s cubic-bezier(.34,1.56,.64,1)';

    if (remaining <= 0) {
      window.location.href = redirectUrl;
    } else {
      setTimeout(tick, 1000);
    }
  }

  // Start after 1s initial delay (let animations settle)
  setTimeout(tick, 1000);

})();
</script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>@yield('title', 'Space Mission Tracker') — Space Tracker</title>
<meta name="description" content="Rastrea misiones y lanzamientos de SpaceX en tiempo real."/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#060b18;--bg2:#0d1526;--bg3:#111d35;
  --surface:#14213d;--surface2:#1a2a50;
  --accent:#4fc3f7;--accent2:#00e5ff;
  --green:#00e676;--amber:#ffb300;--red:#ff5252;--purple:#b39ddb;
  --text:#e8f4fd;--text2:#8baabf;--text3:#4d6a80;
  --border:rgba(79,195,247,.15);--border2:rgba(79,195,247,.30);
  --font-display:'Orbitron',monospace;--font-body:'Inter',sans-serif;
}
html{scroll-behavior:smooth}
body{background:var(--bg);font-family:var(--font-body);color:var(--text);min-height:100vh}

/* ---- NAVBAR ---- */
.navbar{background:var(--bg2);border-bottom:.5px solid var(--border);padding:0 32px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;backdrop-filter:blur(10px)}
.nav-logo{font-family:var(--font-display);font-size:13px;color:var(--accent);letter-spacing:2px;display:flex;align-items:center;gap:8px;text-decoration:none}
.nav-logo svg{width:22px;height:22px}
.nav-links{display:flex;gap:32px}
.nav-link{font-size:12px;color:var(--text2);text-decoration:none;letter-spacing:.5px;transition:color .2s;padding-bottom:2px;border-bottom:1.5px solid transparent}
.nav-link:hover{color:var(--accent)}
.nav-link.active{color:var(--accent);border-bottom-color:var(--accent)}
.nav-badge{background:var(--accent);color:var(--bg);font-size:9px;font-weight:700;padding:3px 10px;border-radius:999px;font-family:var(--font-display);letter-spacing:1px;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.6}}

/* ---- CONTAINERS ---- */
.container{max-width:1200px;margin:0 auto;padding:0 24px}
.page-header{padding:36px 0 0}
.page-title-text{font-family:var(--font-display);font-size:11px;letter-spacing:3px;color:var(--accent);text-transform:uppercase;margin-bottom:6px}
.page-heading{font-size:26px;font-weight:600;color:var(--text);margin-bottom:4px;letter-spacing:-.3px}
.page-sub{font-size:13px;color:var(--text2)}
.section{padding:32px 0}

/* ---- CARDS / SURFACES ---- */
.card{background:var(--surface);border:.5px solid var(--border);border-radius:14px;overflow:hidden}
.card-body{padding:20px}

/* ---- STAT CARDS ---- */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:28px 0}
.stat-card{background:var(--surface);border:.5px solid var(--border);border-radius:14px;padding:20px;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(79,195,247,.08)}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px}
.stat-card.blue::before{background:var(--accent)}.stat-card.green::before{background:var(--green)}.stat-card.amber::before{background:var(--amber)}.stat-card.purple::before{background:var(--purple)}
.stat-label{font-size:9px;color:var(--text3);letter-spacing:1.5px;text-transform:uppercase;font-family:var(--font-display);margin-bottom:10px}
.stat-value{font-size:30px;font-weight:700;color:var(--text);font-family:var(--font-display);margin-bottom:4px;letter-spacing:-1px}
.stat-meta{font-size:11px;color:var(--text2);display:flex;align-items:center;gap:5px}
.dot{display:inline-block;width:6px;height:6px;border-radius:50%}
.dot-blue{background:var(--accent)}.dot-green{background:var(--green)}.dot-amber{background:var(--amber)}.dot-purple{background:var(--purple)}

/* ---- HERO ---- */
.hero{background:linear-gradient(160deg,#0d1e3d 0%,#060b18 60%);padding:48px 0 40px;position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;top:-60px;right:-60px;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(79,195,247,.07) 0%,transparent 70%)}
.hero-eyebrow{font-family:var(--font-display);font-size:10px;letter-spacing:3px;color:var(--accent);margin-bottom:14px}
.hero-title{font-size:38px;font-weight:300;line-height:1.2;margin-bottom:10px;letter-spacing:-.5px}
.hero-title strong{font-weight:700;color:var(--accent2)}
.hero-sub{font-size:14px;color:var(--text2);line-height:1.7;max-width:500px;margin-bottom:28px}

/* ---- COUNTDOWN ---- */
.countdown-bar{background:var(--bg2);border:.5px solid var(--border2);border-radius:14px;padding:22px 28px;margin:0 0 32px;display:flex;align-items:center;gap:24px;flex-wrap:wrap}
.cd-info{flex:1;min-width:200px}
.cd-label{font-family:var(--font-display);font-size:9px;letter-spacing:2px;color:var(--accent);text-transform:uppercase;margin-bottom:4px}
.cd-name{font-size:15px;font-weight:600;color:var(--text)}
.cd-sep{width:.5px;height:44px;background:var(--border)}
.cd-units{display:flex;gap:20px;align-items:center}
.cd-unit{text-align:center}
.cd-num{font-family:var(--font-display);font-size:26px;font-weight:700;color:var(--accent2);line-height:1}
.cd-unit-label{font-size:9px;color:var(--text3);letter-spacing:1px;margin-top:5px}
.cd-colon{font-family:var(--font-display);font-size:22px;color:var(--text3);margin-bottom:12px}

/* ---- SECTION HEADERS ---- */
.section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.section-head h2{font-family:var(--font-display);font-size:11px;letter-spacing:2px;color:var(--text);text-transform:uppercase}
.section-head a{font-size:11px;color:var(--accent);text-decoration:none;transition:opacity .2s}
.section-head a:hover{opacity:.7}

/* ---- LAUNCH CARDS ---- */
.launches-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:32px}
.launch-card{background:var(--surface);border:.5px solid var(--border);border-radius:14px;padding:20px;transition:transform .2s,border-color .2s;position:relative;overflow:hidden}
.launch-card:hover{transform:translateY(-2px);border-color:var(--border2)}
.launch-card::after{content:'';position:absolute;bottom:0;right:0;width:70px;height:70px;border-radius:70px 0 14px 0;background:rgba(79,195,247,.03)}
.launch-num{font-family:var(--font-display);font-size:10px;color:var(--text3);margin-bottom:8px;letter-spacing:1px}
.launch-name{font-weight:600;font-size:16px;color:var(--text);margin-bottom:4px;line-height:1.3}
.launch-rocket{font-size:12px;color:var(--text2);margin-bottom:12px}
.launch-tags{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px}
.launch-date{font-size:11px;color:var(--text3);display:flex;align-items:center;gap:5px}

/* ---- BADGES ---- */
.badge{font-size:10px;padding:3px 10px;border-radius:999px;font-family:var(--font-display);letter-spacing:.5px;font-weight:600;display:inline-block}
.badge-green{background:rgba(0,230,118,.12);color:var(--green);border:.5px solid rgba(0,230,118,.3)}
.badge-red{background:rgba(255,82,82,.12);color:var(--red);border:.5px solid rgba(255,82,82,.3)}
.badge-amber{background:rgba(255,179,0,.12);color:var(--amber);border:.5px solid rgba(255,179,0,.3)}
.badge-blue{background:rgba(79,195,247,.12);color:var(--accent);border:.5px solid rgba(79,195,247,.3)}
.badge-purple{background:rgba(179,157,219,.12);color:var(--purple);border:.5px solid rgba(179,157,219,.3)}

/* ---- TABLE ---- */
.data-table{width:100%;border-collapse:collapse}
.data-table thead tr{border-bottom:.5px solid var(--border2)}
.data-table th{font-family:var(--font-display);font-size:9px;letter-spacing:1.5px;color:var(--text3);text-transform:uppercase;padding:0 14px 14px;text-align:left;font-weight:500}
.data-table td{font-size:13px;color:var(--text);padding:14px;border-bottom:.5px solid rgba(79,195,247,.07)}
.data-table tr:hover td{background:rgba(79,195,247,.03)}
.data-table tr:last-child td{border-bottom:none}
.td-mono{font-family:var(--font-display);font-size:11px;color:var(--text2)}
.td-name{font-weight:500}

/* ---- SEARCH + FILTERS ---- */
.search-bar{background:var(--surface);border:.5px solid var(--border2);border-radius:10px;padding:13px 18px;display:flex;align-items:center;gap:12px;margin-bottom:16px}
.search-bar input{background:none;border:none;outline:none;color:var(--text);font-size:14px;font-family:var(--font-body);flex:1}
.search-bar input::placeholder{color:var(--text3)}
.filter-row{display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap}
.filter-btn{font-size:11px;padding:7px 16px;border-radius:999px;border:.5px solid var(--border2);background:none;color:var(--text2);cursor:pointer;font-family:var(--font-body);transition:all .2s;text-decoration:none;display:inline-block}
.filter-btn:hover,.filter-btn.active{background:rgba(79,195,247,.12);color:var(--accent);border-color:rgba(79,195,247,.3)}

/* ---- PAGINATION ---- */
.pagination{display:flex;gap:6px;justify-content:center;padding:28px 0 8px}
.pg-btn{width:34px;height:34px;border-radius:8px;border:.5px solid var(--border);background:none;color:var(--text2);font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-family:var(--font-display);text-decoration:none;transition:all .2s}
.pg-btn:hover{background:rgba(79,195,247,.08);color:var(--accent)}
.pg-btn.active{background:rgba(79,195,247,.15);color:var(--accent);border-color:rgba(79,195,247,.3)}

/* ---- DETAIL ---- */
.detail-layout{display:grid;grid-template-columns:1fr 340px;gap:24px}
.info-block{background:var(--surface);border:.5px solid var(--border);border-radius:14px;padding:22px;margin-bottom:16px}
.info-block h3{font-family:var(--font-display);font-size:10px;letter-spacing:2px;color:var(--accent);text-transform:uppercase;margin-bottom:16px}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:.5px solid rgba(79,195,247,.07);font-size:13px}
.info-row:last-child{border-bottom:none}
.info-key{color:var(--text2);font-size:12px}
.info-val{color:var(--text);font-weight:500;text-align:right;max-width:55%}
.mission-hero-card{background:linear-gradient(135deg,#0d1e3d 0%,#060b18 100%);border:.5px solid var(--border2);border-radius:14px;padding:30px;margin-bottom:16px;position:relative;overflow:hidden}
.mission-hero-card::before{content:'';position:absolute;right:-20px;top:-20px;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(0,229,255,.06) 0%,transparent 70%)}
.mission-title{font-size:26px;font-weight:600;color:var(--text);margin-bottom:8px;line-height:1.2}
.mission-sub{font-size:13px;color:var(--text2);line-height:1.7}
.timeline{display:flex;flex-direction:column}
.tl-item{display:flex;gap:14px;padding-bottom:20px}
.tl-item:last-child{padding-bottom:0}
.tl-dot-col{display:flex;flex-direction:column;align-items:center}
.tl-dot{width:10px;height:10px;border-radius:50%;background:var(--accent);border:2px solid var(--bg2);flex-shrink:0;margin-top:3px}
.tl-dot.success{background:var(--green)}.tl-dot.failed{background:var(--red)}.tl-dot.pending{background:var(--text3)}
.tl-line{width:1px;flex:1;background:var(--border);margin-top:4px}
.tl-item:last-child .tl-line{display:none}
.tl-label{font-size:12px;font-weight:500;color:var(--text);margin-bottom:2px}
.tl-time{font-size:11px;color:var(--text3);font-family:var(--font-display)}

/* ---- FAVORITES ---- */
.fav-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.fav-card{background:var(--surface);border:.5px solid var(--border);border-radius:14px;padding:20px;transition:border-color .2s}
.fav-card:hover{border-color:var(--border2)}
.fav-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px}
.fav-star{color:var(--amber);font-size:16px}
.fav-name{font-size:15px;font-weight:600;color:var(--text);margin-bottom:3px}
.fav-rocket{font-size:12px;color:var(--text2)}
.fav-bottom{display:flex;align-items:center;justify-content:space-between;margin-top:12px}
.fav-date{font-size:11px;color:var(--text3)}

/* ---- BUTTONS ---- */
.btn-primary{background:rgba(79,195,247,.15);border:.5px solid rgba(79,195,247,.35);color:var(--accent);padding:10px 22px;border-radius:9px;font-size:12px;font-family:var(--font-display);letter-spacing:1px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;text-decoration:none;transition:all .2s}
.btn-primary:hover{background:rgba(79,195,247,.25);border-color:rgba(79,195,247,.5)}
.btn-danger{background:rgba(255,82,82,.08);border:.5px solid rgba(255,82,82,.3);color:var(--red);font-size:11px;padding:5px 12px;border-radius:7px;cursor:pointer;font-family:var(--font-display);letter-spacing:.5px;transition:all .2s;text-decoration:none;display:inline-block}
.btn-danger:hover{background:rgba(255,82,82,.18)}
.btn-ghost{background:none;border:.5px solid var(--border);color:var(--text2);padding:9px 20px;border-radius:9px;font-size:12px;font-family:var(--font-display);letter-spacing:1px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;text-decoration:none;transition:all .2s}
.btn-ghost:hover{border-color:var(--border2);color:var(--text)}

/* ---- ALERTS ---- */
.alert-success{background:rgba(0,230,118,.08);border:.5px solid rgba(0,230,118,.3);color:var(--green);padding:12px 18px;border-radius:10px;font-size:13px;margin-bottom:20px}

/* ---- EMPTY STATE ---- */
.empty-state{text-align:center;padding:70px 20px;color:var(--text3)}
.empty-state p{font-size:13px;margin-top:12px}

/* ---- ROCKET CARDS ---- */
.rockets-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;margin-top:8px}
.rocket-card{background:var(--surface);border:.5px solid var(--border);border-radius:14px;padding:24px;transition:transform .2s,border-color .2s}
.rocket-card:hover{transform:translateY(-2px);border-color:var(--border2)}
.rocket-active{border-color:rgba(0,230,118,.25)}
.rocket-name{font-family:var(--font-display);font-size:15px;color:var(--text);margin-bottom:6px;font-weight:600;letter-spacing:.5px}
.rocket-desc{font-size:13px;color:var(--text2);line-height:1.7;margin-bottom:16px}
.rocket-specs{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;border-top:.5px solid var(--border);padding-top:16px}
.spec-item{}
.spec-label{font-size:9px;color:var(--text3);letter-spacing:1px;text-transform:uppercase;font-family:var(--font-display);margin-bottom:4px}
.spec-val{font-size:14px;font-weight:600;color:var(--text)}

/* ---- AUTH NAV ---- */
.nav-right{display:flex;align-items:center;gap:14px}
.nav-auth{display:flex;align-items:center;gap:10px}
.nav-btn-login{font-size:11px;font-family:var(--font-display);letter-spacing:1px;color:var(--text2);text-decoration:none;padding:7px 14px;border-radius:8px;border:.5px solid var(--border);transition:all .2s}
.nav-btn-login:hover{color:var(--accent);border-color:var(--border2)}
.nav-btn-register{font-size:11px;font-family:var(--font-display);letter-spacing:1px;color:var(--accent);text-decoration:none;padding:7px 14px;border-radius:8px;border:.5px solid rgba(79,195,247,.35);background:rgba(79,195,247,.10);transition:all .2s}
.nav-btn-register:hover{background:rgba(79,195,247,.20);border-color:rgba(79,195,247,.5)}
.nav-user{position:relative}
.nav-user-toggle{display:flex;align-items:center;gap:8px;cursor:pointer;background:none;border:.5px solid var(--border);padding:6px 12px;border-radius:999px;font-family:var(--font-body);color:var(--text2);font-size:12px;transition:all .2s}
.nav-user-toggle:hover{color:var(--accent);border-color:var(--border2)}
.nav-user-avatar{width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));color:var(--bg);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:10px;font-weight:700}
.nav-user-menu{position:absolute;top:calc(100% + 8px);right:0;background:var(--surface2);border:.5px solid var(--border2);border-radius:10px;padding:8px;min-width:210px;display:none;box-shadow:0 12px 40px rgba(0,0,0,.4);z-index:200}
.nav-user-menu.open{display:block}
.nav-user-menu .nu-head{padding:8px 12px 10px;border-bottom:.5px solid var(--border);margin-bottom:6px}
.nav-user-menu .nu-name{font-size:13px;color:var(--text);font-weight:600}
.nav-user-menu .nu-email{font-size:11px;color:var(--text3);margin-top:2px;word-break:break-all}
.nav-user-menu a,.nav-user-menu button{display:block;width:100%;text-align:left;background:none;border:none;color:var(--text2);font-size:12px;padding:8px 12px;border-radius:7px;cursor:pointer;font-family:var(--font-body);text-decoration:none;transition:all .15s}
.nav-user-menu a:hover,.nav-user-menu button:hover{background:rgba(79,195,247,.08);color:var(--accent)}
.nav-user-menu .nu-danger:hover{background:rgba(255,82,82,.10);color:var(--red)}

/* ---- RESPONSIVE ---- */
@media(max-width:900px){
  .launches-grid,.detail-layout,.rockets-grid{grid-template-columns:1fr}
  .stats-grid{grid-template-columns:repeat(2,1fr)}
  .fav-grid{grid-template-columns:1fr}
  .navbar{padding:0 16px}
  .hero-title{font-size:28px}
  .nav-btn-register{display:none}
}
</style>
</head>
<body>

<nav class="navbar">
  <a href="{{ route('dashboard') }}" class="nav-logo">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L8 8l-6 2 4 4-1 6 7-3 7 3-1-6 4-4-6-2-4-6z"/></svg>
    SPACE TRACKER
  </a>
  <div class="nav-links">
    <a href="{{ route('dashboard') }}"    class="nav-link {{ request()->routeIs('dashboard')       ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('launches.index') }}" class="nav-link {{ request()->routeIs('launches*')     ? 'active' : '' }}">Lanzamientos</a>
    <a href="{{ route('rockets.index') }}"  class="nav-link {{ request()->routeIs('rockets*')      ? 'active' : '' }}">Cohetes</a>
    @auth
      <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites*')   ? 'active' : '' }}">Favoritos</a>
    @endauth
  </div>

  <div class="nav-right">
    <span class="nav-badge">LIVE</span>

    @auth
      @php $u = auth()->user(); $initial = strtoupper(mb_substr($u->name, 0, 1)); @endphp
      <div class="nav-user" id="navUser">
        <button type="button" class="nav-user-toggle" onclick="document.getElementById('navUserMenu').classList.toggle('open')">
          <span class="nav-user-avatar">{{ $initial }}</span>
          <span>{{ \Illuminate\Support\Str::limit($u->name, 14) }}</span>
          <span style="font-size:9px;color:var(--text3)">▼</span>
        </button>
        <div class="nav-user-menu" id="navUserMenu">
          <div class="nu-head">
            <div class="nu-name">{{ $u->name }}</div>
            <div class="nu-email">{{ $u->email }}</div>
          </div>
          <a href="{{ route('favorites.index') }}">★ Mis favoritos</a>
          <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="nu-danger">⏻ Cerrar sesión</button>
          </form>
        </div>
      </div>
    @else
      <div class="nav-auth">
        <a href="{{ route('login') }}"    class="nav-btn-login">⏵ ACCEDER</a>
        <a href="{{ route('register') }}" class="nav-btn-register">REGISTRO</a>
      </div>
    @endauth
  </div>
</nav>

<main>
  @if(session('success'))
    <div class="container"><div class="alert-success" style="margin-top:16px">✓ {{ session('success') }}</div></div>
  @endif

  @yield('content')
</main>

<script>
// Countdown timer live update
function updateCountdown() {
  const el = document.getElementById('countdown-target');
  if (!el) return;
  const target = new Date(el.dataset.target);
  const now = new Date();
  const diff = target - now;
  if (diff <= 0) { el.innerHTML = '<span style="color:var(--green);font-family:var(--font-display);font-size:13px">¡EN CURSO!</span>'; return; }
  const d = Math.floor(diff/86400000);
  const h = Math.floor((diff%86400000)/3600000);
  const m = Math.floor((diff%3600000)/60000);
  const s = Math.floor((diff%60000)/1000);
  el.querySelector('#cd-d').textContent = String(d).padStart(2,'0');
  el.querySelector('#cd-h').textContent = String(h).padStart(2,'0');
  el.querySelector('#cd-m').textContent = String(m).padStart(2,'0');
  el.querySelector('#cd-s').textContent = String(s).padStart(2,'0');
}
updateCountdown();
setInterval(updateCountdown, 1000);

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
  const menu = document.getElementById('navUserMenu');
  const user = document.getElementById('navUser');
  if (menu && user && !user.contains(e.target)) {
    menu.classList.remove('open');
  }
});
</script>

@yield('scripts')
</body>
</html>

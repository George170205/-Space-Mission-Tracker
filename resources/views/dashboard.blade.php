@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
{{-- HERO --}}
<div class="hero">
  <div class="container">
    <div class="hero-eyebrow">⟡ SPACE MISSION TRACKER</div>
    <h1 class="hero-title">Rastreo de <strong>Misiones</strong><br>Espaciales en Tiempo Real</h1>
    <p class="hero-sub">Consulta lanzamientos pasados y futuros de SpaceX, detalles de cohetes, cargas útiles y mucho más.</p>
    <a href="{{ route('launches.index') }}" class="btn-primary">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
      Explorar misiones
    </a>
  </div>
</div>

<div class="container">

  {{-- STAT CARDS --}}
  <div class="stats-grid">
    <div class="stat-card blue">
      <div class="stat-label">Total lanzamientos</div>
      <div class="stat-value">{{ $stats['total'] }}</div>
      <div class="stat-meta"><span class="dot dot-blue"></span>Desde 2006</div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">Exitosos</div>
      <div class="stat-value">{{ $stats['successful'] }}</div>
      <div class="stat-meta"><span class="dot dot-green"></span>{{ $successRate }}% de éxito</div>
    </div>
    <div class="stat-card amber">
      <div class="stat-label">Próximos</div>
      <div class="stat-value">{{ $stats['upcoming'] }}</div>
      <div class="stat-meta"><span class="dot dot-amber"></span>Lanzamientos planeados</div>
    </div>
    <div class="stat-card purple">
      <div class="stat-label">Fallidos</div>
      <div class="stat-value">{{ $stats['failed'] }}</div>
      <div class="stat-meta"><span class="dot dot-purple"></span>Total de fallos</div>
    </div>
  </div>

  {{-- COUNTDOWN --}}
  @if($next)
  <div class="countdown-bar">
    <div class="cd-info">
      <div class="cd-label">Próximo lanzamiento</div>
      <div class="cd-name">{{ $next['name'] ?? '—' }} · {{ $next['rocket']['name'] ?? '' }}</div>
    </div>
    <div class="cd-sep"></div>
    @if($countdown)
    <div class="cd-units" id="countdown-target" data-target="{{ $next['date_utc'] }}">
      <div class="cd-unit"><div class="cd-num" id="cd-d">{{ str_pad($countdown['days'], 2, '0', STR_PAD_LEFT) }}</div><div class="cd-unit-label">Días</div></div>
      <div class="cd-colon">:</div>
      <div class="cd-unit"><div class="cd-num" id="cd-h">{{ str_pad($countdown['hours'], 2, '0', STR_PAD_LEFT) }}</div><div class="cd-unit-label">Horas</div></div>
      <div class="cd-colon">:</div>
      <div class="cd-unit"><div class="cd-num" id="cd-m">{{ str_pad($countdown['minutes'], 2, '0', STR_PAD_LEFT) }}</div><div class="cd-unit-label">Min</div></div>
      <div class="cd-colon">:</div>
      <div class="cd-unit"><div class="cd-num" id="cd-s">{{ str_pad($countdown['seconds'], 2, '0', STR_PAD_LEFT) }}</div><div class="cd-unit-label">Seg</div></div>
    </div>
    @else
    <div style="font-size:13px;color:var(--text2)">Fecha por confirmar</div>
    @endif
    <span class="badge badge-amber" style="margin-left:auto">PRÓXIMO</span>
  </div>
  @endif

  {{-- RECENT LAUNCHES --}}
  <div class="section-head">
    <h2>Lanzamientos recientes</h2>
    <a href="{{ route('launches.index') }}">Ver todos →</a>
  </div>

  <div class="launches-grid">
    @forelse($recent as $launch)
    @php
      $success = $launch['success'] ?? null;
      $upcoming = $launch['upcoming'] ?? false;
      if ($upcoming) { $badgeClass = 'badge-amber'; $badgeLabel = 'Próximo'; }
      elseif ($success === true) { $badgeClass = 'badge-green'; $badgeLabel = '✓ Exitoso'; }
      elseif ($success === false) { $badgeClass = 'badge-red'; $badgeLabel = '✗ Fallido'; }
      else { $badgeClass = 'badge-amber'; $badgeLabel = '~ Parcial'; }
      $dateStr = !empty($launch['date_utc']) ? \Carbon\Carbon::parse($launch['date_utc'])->format('d M Y') : '—';
      $rocketName = $launch['rocket']['name'] ?? '—';
      $padName = $launch['launchpad']['name'] ?? '';
    @endphp
    <div class="launch-card">
      <div class="launch-num">LANZAMIENTO #{{ $launch['flight_number'] ?? '' }}</div>
      <div class="launch-name">{{ $launch['name'] ?? '—' }}</div>
      <div class="launch-rocket">{{ $rocketName }}{{ $padName ? ' · '.$padName : '' }}</div>
      <div class="launch-tags">
        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
      </div>
      <div class="launch-date">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        {{ $dateStr }}
      </div>
      <a href="{{ route('launches.show', $launch['id']) }}" class="btn-primary" style="margin-top:14px;font-size:10px;padding:7px 14px">Ver detalles →</a>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text3)">No se pudieron cargar los lanzamientos. Verifica tu conexión a internet.</div>
    @endforelse
  </div>

</div>
@endsection

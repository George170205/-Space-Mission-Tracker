@extends('layouts.app')
@section('title', $launch['name'] ?? 'Detalle de misión')

@section('content')
<div class="container">
  <div class="page-header">
    <a href="{{ route('launches.index') }}" style="font-size:12px;color:var(--accent);text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:20px">
      ← Volver a lanzamientos
    </a>
  </div>

  @php
    $success = $launch['success'] ?? null;
    $upcoming = $launch['upcoming'] ?? false;
    if ($upcoming) { $bc='badge-amber'; $bl='Próximo'; }
    elseif ($success===true) { $bc='badge-green'; $bl='✓ Exitoso'; }
    elseif ($success===false) { $bc='badge-red'; $bl='✗ Fallido'; }
    else { $bc='badge-amber'; $bl='~ Parcial'; }
    $dateStr = !empty($launch['date_utc']) ? \Carbon\Carbon::parse($launch['date_utc'])->format('d M Y · H:i').' UTC' : '—';
    $rocket = $launch['rocket_data'] ?? [];
    $pad = $launch['launchpad_data'] ?? [];
  @endphp

  <div class="detail-layout" style="padding-bottom:48px">
    {{-- MAIN --}}
    <div>
      <div class="mission-hero-card">
        <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
          <span class="badge {{ $bc }}">{{ $bl }}</span>
          @if(!empty($launch['links']['wikipedia']))
            <a href="{{ $launch['links']['wikipedia'] }}" target="_blank" class="badge badge-blue" style="text-decoration:none">Wikipedia ↗</a>
          @endif
        </div>
        <div class="mission-title">{{ $launch['name'] ?? '—' }}</div>
        @if(!empty($launch['details']))
          <div class="mission-sub" style="margin-top:10px">{{ $launch['details'] }}</div>
        @endif
      </div>

      {{-- FLIGHT DATA --}}
      <div class="info-block">
        <h3>Datos del vuelo</h3>
        <div class="info-row"><span class="info-key">Número de vuelo</span><span class="info-val td-mono">#{{ $launch['flight_number'] ?? '—' }}</span></div>
        <div class="info-row"><span class="info-key">Fecha de lanzamiento</span><span class="info-val">{{ $dateStr }}</span></div>
        <div class="info-row"><span class="info-key">Sitio de lanzamiento</span><span class="info-val">{{ $pad['full_name'] ?? $pad['name'] ?? '—' }}</span></div>
        <div class="info-row"><span class="info-key">Resultado</span>
          <span class="info-val" style="color:{{ $success===true ? 'var(--green)' : ($success===false ? 'var(--red)' : 'var(--amber)') }}">{{ $bl }}</span>
        </div>
        @if(!empty($launch['links']['webcast']))
        <div class="info-row"><span class="info-key">Webcast</span>
          <a href="{{ $launch['links']['webcast'] }}" target="_blank" style="color:var(--accent);font-size:13px;text-decoration:none">Ver transmisión ↗</a>
        </div>
        @endif
      </div>

      {{-- TIMELINE --}}
      @if(!empty($launch['cores']))
      <div class="info-block">
        <h3>Cores / Propulsores</h3>
        <div class="timeline">
          @foreach($launch['cores'] as $core)
          <div class="tl-item">
            <div class="tl-dot-col">
              <div class="tl-dot {{ $core['landing_success'] ? 'success' : 'pending' }}"></div>
              <div class="tl-line"></div>
            </div>
            <div style="flex:1;padding-bottom:16px">
              <div class="tl-label">Core {{ $core['core'] ?? '—' }}</div>
              <div class="tl-time">Vuelo #{{ $core['flight'] ?? '?' }} · Aterrizaje: {{ $core['landing_success'] ? 'Exitoso ✓' : 'No' }}</div>
              @if(!empty($core['landpad']))<div class="tl-time" style="margin-top:2px">Pad: {{ $core['landpad'] }}</div>@endif
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    {{-- SIDEBAR --}}
    <div>
      {{-- ROCKET --}}
      @if(!empty($rocket))
      <div class="info-block">
        <h3>Cohete</h3>
        <div class="info-row"><span class="info-key">Nombre</span><span class="info-val">{{ $rocket['name'] ?? '—' }}</span></div>
        <div class="info-row"><span class="info-key">Tipo</span><span class="info-val">{{ $rocket['type'] ?? '—' }}</span></div>
        <div class="info-row"><span class="info-key">Altura</span><span class="info-val">{{ $rocket['height']['meters'] ?? '—' }} m</span></div>
        <div class="info-row"><span class="info-key">Masa</span><span class="info-val">{{ number_format($rocket['mass']['kg'] ?? 0) }} kg</span></div>
        <div class="info-row"><span class="info-key">Etapas</span><span class="info-val">{{ $rocket['stages'] ?? '—' }}</span></div>
        <div class="info-row"><span class="info-key">Primer vuelo</span><span class="info-val">{{ $rocket['first_flight'] ?? '—' }}</span></div>
        <div class="info-row"><span class="info-key">Estado</span>
          <span class="info-val" style="color:{{ ($rocket['active']??false)?'var(--green)':'var(--text3)' }}">
            {{ ($rocket['active']??false) ? 'Activo' : 'Inactivo' }}
          </span>
        </div>
      </div>
      @endif

      {{-- PAYLOADS --}}
      @if(!empty($launch['payloads']))
      <div class="info-block">
        <h3>Cargas (Payloads)</h3>
        @foreach(array_slice($launch['payloads'], 0, 3) as $pl)
        <div class="info-row"><span class="info-key">ID</span><span class="info-val td-mono" style="font-size:11px">{{ is_string($pl) ? $pl : ($pl['id']??'—') }}</span></div>
        @endforeach
      </div>
      @endif

      {{-- FAVORITE BUTTON --}}
      @if(!$isFavorite)
      <form method="POST" action="{{ route('favorites.store') }}">
        @csrf
        <input type="hidden" name="launch_id"    value="{{ $launch['id'] }}">
        <input type="hidden" name="mission_name" value="{{ $launch['name'] ?? '' }}">
        <input type="hidden" name="rocket_name"  value="{{ $rocket['name'] ?? '' }}">
        <input type="hidden" name="launch_date"  value="{{ $dateStr }}">
        <input type="hidden" name="launch_site"  value="{{ $pad['full_name'] ?? $pad['name'] ?? '' }}">
        <input type="hidden" name="success"      value="{{ $success === true ? '1' : ($success === false ? '0' : '') }}">
        <input type="hidden" name="status_label" value="{{ $bl }}">
        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;margin-bottom:10px">☆ Guardar en favoritos</button>
      </form>
      @else
      <div class="btn-primary" style="width:100%;justify-content:center;margin-bottom:10px;opacity:.7;cursor:default">★ Ya en favoritos</div>
      @endif

      @if(!empty($launch['links']['webcast']))
      <a href="{{ $launch['links']['webcast'] }}" target="_blank" class="btn-ghost" style="width:100%;justify-content:center">↗ Ver en YouTube</a>
      @endif
    </div>
  </div>
</div>
@endsection

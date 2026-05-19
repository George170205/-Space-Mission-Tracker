@extends('layouts.app')
@section('title', 'Lanzamientos')

@section('content')
<div class="container">
  <div class="page-header">
    <div class="page-title-text">SpaceX API</div>
    <div class="page-heading">Todos los lanzamientos</div>
    <div class="page-sub">{{ $total }} misiones encontradas</div>
  </div>

  <div class="section">
    {{-- SEARCH --}}
    <form method="GET" action="{{ route('launches.index') }}" id="search-form">
      <div class="search-bar">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text3)"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" name="search" placeholder="Buscar misión, cohete..." value="{{ $search }}" autocomplete="off"/>
        <input type="hidden" name="filter" value="{{ $filter }}">
        <button type="submit" class="btn-primary" style="padding:6px 16px;font-size:10px">Buscar</button>
      </div>

      {{-- FILTERS --}}
      <div class="filter-row">
        @foreach(['all'=>'Todos','exitoso'=>'Exitosos','fallido'=>'Fallidos','proximo'=>'Próximos','falcon9'=>'Falcon 9','falconheavy'=>'Falcon Heavy','starship'=>'Starship'] as $val => $label)
        <a href="{{ route('launches.index', ['filter'=>$val, 'search'=>$search]) }}"
           class="filter-btn {{ $filter === $val ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>
    </form>

    {{-- TABLE --}}
    <div class="card">
      <div class="card-body" style="padding:0">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Misión</th>
              <th>Cohete</th>
              <th>Fecha</th>
              <th>Sitio</th>
              <th>Estado</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            @forelse($paginated as $launch)
            @php
              $success = $launch['success'] ?? null;
              $upcoming = $launch['upcoming'] ?? false;
              if ($upcoming) { $bc = 'badge-amber'; $bl = 'Próximo'; }
              elseif ($success === true) { $bc = 'badge-green'; $bl = 'Exitoso'; }
              elseif ($success === false) { $bc = 'badge-red'; $bl = 'Fallido'; }
              else { $bc = 'badge-amber'; $bl = 'Parcial'; }
              $dateStr = !empty($launch['date_utc']) ? \Carbon\Carbon::parse($launch['date_utc'])->format('d M Y') : '—';
              $rocketName = $launch['rocket']['name'] ?? '—';
              $padName = $launch['launchpad']['name'] ?? '—';
              $isFav = in_array($launch['id'], $favoriteIds);
            @endphp
            <tr>
              <td class="td-mono">#{{ $launch['flight_number'] ?? '?' }}</td>
              <td class="td-name">{{ $launch['name'] ?? '—' }} @if($isFav)<span style="color:var(--amber);margin-left:4px" title="En favoritos">★</span>@endif</td>
              <td class="td-mono">{{ $rocketName }}</td>
              <td class="td-mono">{{ $dateStr }}</td>
              <td style="font-size:12px;color:var(--text2)">{{ $padName }}</td>
              <td><span class="badge {{ $bc }}">{{ $bl }}</span></td>
              <td><a href="{{ route('launches.show', $launch['id']) }}" style="font-size:11px;color:var(--accent)">Ver →</a></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text3)">No se encontraron lanzamientos con ese criterio.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- PAGINATION --}}
    @if($pages > 1)
    <div class="pagination">
      @if($page > 1)
        <a class="pg-btn" href="{{ route('launches.index', ['filter'=>$filter,'search'=>$search,'page'=>$page-1]) }}">‹</a>
      @endif
      @for($i = max(1,$page-2); $i <= min($pages,$page+2); $i++)
        <a class="pg-btn {{ $i==$page?'active':'' }}" href="{{ route('launches.index', ['filter'=>$filter,'search'=>$search,'page'=>$i]) }}">{{ $i }}</a>
      @endfor
      @if($page < $pages)
        <a class="pg-btn" href="{{ route('launches.index', ['filter'=>$filter,'search'=>$search,'page'=>$page+1]) }}">›</a>
      @endif
    </div>
    @endif
  </div>
</div>
@endsection

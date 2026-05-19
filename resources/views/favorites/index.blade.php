@extends('layouts.app')
@section('title', 'Mis Favoritos')

@section('content')
<div class="container">
  <div class="page-header">
    <div class="page-title-text">Base de datos local</div>
    <div class="page-heading">Mis Favoritos</div>
    <div class="page-sub">Misiones guardadas: {{ $favorites->count() }}</div>
  </div>

  <div class="section">
    @if($favorites->isEmpty())
    <div class="empty-state">
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin:0 auto 16px;display:block;opacity:.2;color:var(--accent)">
        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01L12 2z"/>
      </svg>
      <p>Todavía no tienes misiones favoritas.</p>
      <a href="{{ route('launches.index') }}" class="btn-primary" style="margin-top:16px">Explorar lanzamientos</a>
    </div>
    @else

    <div class="fav-grid">
      @foreach($favorites as $fav)
      @php
        $bc = match($fav->status_label) {
          'Exitoso','✓ Exitoso' => 'badge-green',
          'Fallido','✗ Fallido' => 'badge-red',
          'Próximo' => 'badge-amber',
          default => 'badge-amber',
        };
      @endphp
      <div class="fav-card">
        <div class="fav-top">
          <div>
            <div class="fav-name">{{ $fav->mission_name }}</div>
            <div class="fav-rocket">{{ $fav->rocket_name }}</div>
          </div>
          <span class="fav-star">★</span>
        </div>
        <div class="launch-tags">
          <span class="badge {{ $bc }}">{{ $fav->status_label ?? '—' }}</span>
        </div>
        <div class="fav-bottom">
          <div class="fav-date">{{ $fav->launch_date }}</div>
          <div style="display:flex;gap:8px;align-items:center">
            <a href="{{ route('launches.show', $fav->launch_id) }}" style="font-size:11px;color:var(--accent);text-decoration:none">Ver →</a>
            <form method="POST" action="{{ route('favorites.destroy', $fav->id) }}" onsubmit="return confirm('¿Quitar de favoritos?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn-danger">✕ Quitar</button>
            </form>
          </div>
        </div>

        {{-- NOTES --}}
        <form method="POST" action="{{ route('favorites.notes', $fav->id) }}" style="margin-top:14px;border-top:.5px solid var(--border);padding-top:14px">
          @csrf
          <div style="font-size:9px;color:var(--text3);letter-spacing:1px;text-transform:uppercase;font-family:var(--font-display);margin-bottom:6px">Notas personales</div>
          <textarea name="notes" rows="2"
            style="width:100%;background:var(--bg3);border:.5px solid var(--border2);border-radius:8px;color:var(--text);font-size:12px;padding:8px 10px;resize:vertical;font-family:var(--font-body);outline:none"
            placeholder="Agrega notas sobre esta misión...">{{ $fav->notes }}</textarea>
          <button type="submit" class="btn-primary" style="font-size:10px;padding:6px 14px;margin-top:8px">Guardar nota</button>
        </form>
      </div>
      @endforeach
    </div>

    @endif
  </div>
</div>
@endsection

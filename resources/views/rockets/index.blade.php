@extends('layouts.app')
@section('title', 'Cohetes')

@section('content')
<div class="container">
  <div class="page-header">
    <div class="page-title-text">SpaceX API</div>
    <div class="page-heading">Cohetes SpaceX</div>
    <div class="page-sub">{{ count($rockets) }} cohetes en el registro</div>
  </div>

  <div class="section">
    <div class="rockets-grid">
      @forelse($rockets as $rocket)
      @php
        $active   = $rocket['active'] ?? false;
        $heightM  = $rocket['height']['meters'] ?? null;
        $massKg   = $rocket['mass']['kg'] ?? null;
        $thrustKN = $rocket['engines']['thrust_sea_level']['kN'] ?? null;
      @endphp
      <div class="rocket-card {{ $active ? 'rocket-active' : '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <div class="rocket-name">{{ $rocket['name'] ?? '—' }}</div>
          <span class="badge {{ $active ? 'badge-green' : 'badge-red' }}">{{ $active ? 'Activo' : 'Inactivo' }}</span>
        </div>
        <div class="rocket-desc">{{ Str::limit($rocket['description'] ?? '', 220) }}</div>
        <div class="rocket-specs">
          <div class="spec-item">
            <div class="spec-label">Altura</div>
            <div class="spec-val">{{ $heightM ? $heightM.' m' : '—' }}</div>
          </div>
          <div class="spec-item">
            <div class="spec-label">Masa</div>
            <div class="spec-val">{{ $massKg ? number_format($massKg/1000,0).'t' : '—' }}</div>
          </div>
          <div class="spec-item">
            <div class="spec-label">Empuje</div>
            <div class="spec-val">{{ $thrustKN ? $thrustKN.' kN' : '—' }}</div>
          </div>
          <div class="spec-item">
            <div class="spec-label">Etapas</div>
            <div class="spec-val">{{ $rocket['stages'] ?? '—' }}</div>
          </div>
          <div class="spec-item">
            <div class="spec-label">Motores</div>
            <div class="spec-val">{{ $rocket['engines']['number'] ?? '—' }}</div>
          </div>
          <div class="spec-item">
            <div class="spec-label">Primer vuelo</div>
            <div class="spec-val" style="font-size:12px">{{ $rocket['first_flight'] ?? '—' }}</div>
          </div>
        </div>
        @if(!empty($rocket['flickr_images'][0]))
        <img src="{{ $rocket['flickr_images'][0] }}" alt="{{ $rocket['name'] }}"
             style="width:100%;height:180px;object-fit:cover;border-radius:10px;margin-top:16px;border:.5px solid var(--border)">
        @endif
        @if(!empty($rocket['wikipedia']))
        <a href="{{ $rocket['wikipedia'] }}" target="_blank" class="btn-ghost" style="margin-top:14px;font-size:10px;padding:7px 14px">Wikipedia ↗</a>
        @endif
      </div>
      @empty
      <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text3)">
        No se pudieron cargar los cohetes.
      </div>
      @endforelse
    </div>
  </div>
</div>
@endsection

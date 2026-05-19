@extends('layouts.app')
@section('title', 'Acceso al Centro de Control')

@section('content')
<div class="container">
  <div style="max-width:440px;margin:60px auto;padding:0 16px">

    <div class="page-header" style="text-align:center;padding-top:0">
      <div class="page-title-text">Mission Control · Autenticación</div>
      <div class="page-heading" style="font-size:28px">Iniciar sesión</div>
      <div class="page-sub">Accede a tu base de operaciones</div>
    </div>

    <div class="card" style="margin-top:28px">
      <div class="card-body" style="padding:28px">

        @if($errors->any())
          <div class="alert-success" style="background:rgba(255,82,82,.08);border-color:rgba(255,82,82,.3);color:var(--red);margin-bottom:18px">
            ✕ {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

          {{-- EMAIL --}}
          <div style="margin-bottom:18px">
            <label style="display:block;font-family:var(--font-display);font-size:10px;color:var(--text3);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px">Correo electrónico</label>
            <input type="email" name="email" required autofocus value="{{ old('email') }}"
              placeholder="comandante@spacex.com"
              style="width:100%;background:var(--bg3);border:.5px solid var(--border2);border-radius:9px;color:var(--text);font-size:14px;padding:12px 14px;font-family:var(--font-body);outline:none;transition:border-color .2s"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border2)'"/>
          </div>

          {{-- PASSWORD --}}
          <div style="margin-bottom:18px">
            <label style="display:block;font-family:var(--font-display);font-size:10px;color:var(--text3);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px">Contraseña</label>
            <input type="password" name="password" required placeholder="••••••••"
              style="width:100%;background:var(--bg3);border:.5px solid var(--border2);border-radius:9px;color:var(--text);font-size:14px;padding:12px 14px;font-family:var(--font-body);outline:none;transition:border-color .2s"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border2)'"/>
          </div>

          {{-- REMEMBER --}}
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
            <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text2);cursor:pointer">
              <input type="checkbox" name="remember" value="1" style="accent-color:var(--accent)">
              Recordarme
            </label>
            <a href="{{ route('register') }}" style="font-size:11px;color:var(--accent);text-decoration:none">¿Sin cuenta? Crea una →</a>
          </div>

          <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:12px">
            ⏵ ACCEDER
          </button>
        </form>

      </div>
    </div>

    <div style="text-align:center;margin-top:18px;font-size:11px;color:var(--text3);letter-spacing:.5px">
      Conexión cifrada · Space Tracker v1.0
    </div>

  </div>
</div>
@endsection

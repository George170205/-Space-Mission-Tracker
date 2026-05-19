@extends('layouts.app')
@section('title', 'Registro de Comandante')

@section('content')
<div class="container">
  <div style="max-width:480px;margin:60px auto;padding:0 16px">

    <div class="page-header" style="text-align:center;padding-top:0">
      <div class="page-title-text">Mission Control · Nuevo acceso</div>
      <div class="page-heading" style="font-size:28px">Crear cuenta</div>
      <div class="page-sub">Únete a la flota de operadores</div>
    </div>

    <div class="card" style="margin-top:28px">
      <div class="card-body" style="padding:28px">

        @if($errors->any())
          <div class="alert-success" style="background:rgba(255,82,82,.08);border-color:rgba(255,82,82,.3);color:var(--red);margin-bottom:18px">
            <ul style="margin:0;padding-left:18px">
              @foreach($errors->all() as $err)
                <li style="font-size:12px">{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
          @csrf

          {{-- NAME --}}
          <div style="margin-bottom:18px">
            <label style="display:block;font-family:var(--font-display);font-size:10px;color:var(--text3);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px">Nombre del operador</label>
            <input type="text" name="name" required autofocus value="{{ old('name') }}"
              placeholder="Cmdr. Abel Ramírez"
              style="width:100%;background:var(--bg3);border:.5px solid var(--border2);border-radius:9px;color:var(--text);font-size:14px;padding:12px 14px;font-family:var(--font-body);outline:none;transition:border-color .2s"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border2)'"/>
          </div>

          {{-- EMAIL --}}
          <div style="margin-bottom:18px">
            <label style="display:block;font-family:var(--font-display);font-size:10px;color:var(--text3);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px">Correo electrónico</label>
            <input type="email" name="email" required value="{{ old('email') }}"
              placeholder="comandante@spacex.com"
              style="width:100%;background:var(--bg3);border:.5px solid var(--border2);border-radius:9px;color:var(--text);font-size:14px;padding:12px 14px;font-family:var(--font-body);outline:none;transition:border-color .2s"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border2)'"/>
          </div>

          {{-- PASSWORD --}}
          <div style="margin-bottom:18px">
            <label style="display:block;font-family:var(--font-display);font-size:10px;color:var(--text3);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px">Contraseña (mín. 6)</label>
            <input type="password" name="password" required placeholder="••••••••"
              style="width:100%;background:var(--bg3);border:.5px solid var(--border2);border-radius:9px;color:var(--text);font-size:14px;padding:12px 14px;font-family:var(--font-body);outline:none;transition:border-color .2s"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border2)'"/>
          </div>

          {{-- PASSWORD CONFIRM --}}
          <div style="margin-bottom:24px">
            <label style="display:block;font-family:var(--font-display);font-size:10px;color:var(--text3);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:8px">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required placeholder="••••••••"
              style="width:100%;background:var(--bg3);border:.5px solid var(--border2);border-radius:9px;color:var(--text);font-size:14px;padding:12px 14px;font-family:var(--font-body);outline:none;transition:border-color .2s"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border2)'"/>
          </div>

          <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:12px">
            ⏵ REGISTRAR Y ACCEDER
          </button>

          <div style="text-align:center;margin-top:18px">
            <a href="{{ route('login') }}" style="font-size:11px;color:var(--accent);text-decoration:none">← ¿Ya tienes cuenta? Inicia sesión</a>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
@if (session('status'))
    <div class="alert alert-success mb-4" role="alert">
        {{ session('status') }}
    </div>
@endif
<div class="container row">
    <div class="col-md-1"></div>
    <div class="text-center col-md-5 my-5">
        <h2 id="textLogin"><b>{{ __('Iniciar sesión') }}</b></h2>
        <hr>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group text-left">
                <label id="labelLogin">{{ __('Correo electrónico') }}</label>
                <input id="inputLogin" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            
            <div class="form-group text-left">
                <label id="labelLogin">{{ __('Contraseña') }}</label>
                <input id="inputLogin" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            

            <!-- <div class="form-group row">
                <div class="col-md-6 offset-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
            </div> -->

            <div class="d-grid gap-2 col-12 mx-auto">
                <button type="submit" class="btn" id="btnLogin">
                    {{ __('Acceder') }}
                </button>
            </div>
            <!-- @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif -->
        </form>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-4 my-5">
        <div class="card border-danger mt-5 mb-3">
            <div class="card-body">
                <h4>Requisitos</h4>
                <ul>
                    <li>
                        <p>Acceder con la versión más recientes de alguno de los siguientes exploradores: </p>
                        <ul>
                            <li>Firefox</li>
                            <li>Chrome</li>
                            <li>Microsoft Edge</li>
                            <li>Safari</li>
                        </ul>
                    </li>
                    <li>
                        <p>Verificar que no este activo el traductor del navegador.</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

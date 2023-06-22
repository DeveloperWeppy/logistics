@extends('others.layout_others.master')

@section('title')
    Iniciar Sesion
@endsection
@section('others-content')
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card">
                    <div>
                        <div><a class="logo" href="{{ route('login.login') }}"><img class="img-fluid for-light"
                                    src="{{ asset('assets/images/logo/LOGO80px.png') }}" alt="logo image"></a></div>
                        <div class="login-main">
                            <form class="theme-form"  action="{{ route('login.perform') }}" method="POST">
                                @csrf
                                <h2 class="text-center">Iniciar Sesión</h2>
                                <p class="text-center">Ingrese su correo electrónico y contraseña para iniciar sesión</p>
                                <div class="form-group">
                                    <label class="col-form-label">Correo electrónico</label>
                                    <input class="form-control" type="email" required="" name="email" placeholder="Test@gmail.com">
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Contraseña</label>
                                    <div class="form-input position-relative">
                                        <input class="form-control" type="password" name="password" required=""
                                            placeholder="*********">
                                        <div class="show-hide"><span class="show"> </span></div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <div class="checkbox p-0">
                                        <input id="checkbox1" type="checkbox">
                                        <label class="text-muted" for="checkbox1">Recordar Contraseña</label>
                                    </div>
                                    {{-- <a class="link" href="">Forgot password?</a> --}}
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Ingresar </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

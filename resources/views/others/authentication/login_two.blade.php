@extends('others.layout_others.master')

@section('others-content')
<div class="container-fluid">
    <div class="row">
      <div class="col-xl-5"><img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/3.jpg') }}" alt="bg3"></div>
      <div class="col-xl-7 p-0">
        <div class="login-card">
          <div>
            <div><a class="logo" href="{{ route('dashboard') }}"><img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt="logo image"></a></div>
            <div class="login-main">
              <form class="theme-form">
                <h2 class="text-center">Sign in to account</h2>
                <p class="text-center">Enter your email & password to login</p>
                <div class="form-group">
                  <label class="col-form-label">Email Address</label>
                  <input class="form-control" type="email" required="" placeholder="Test@gmail.com">
                </div>
                <div class="form-group">
                  <label class="col-form-label">Password</label>
                  <div class="form-input position-relative">
                    <input class="form-control" type="password" name="login[password]" required="" placeholder="*********">
                    <div class="show-hide"><span class="show">                         </span></div>
                  </div>
                </div>
                <div class="form-group mb-0">
                  <div class="checkbox p-0">
                    <input id="checkbox1" type="checkbox">
                    <label class="text-muted" for="checkbox1">Remember password</label>
                  </div><a class="link" href="{{ route('forget-password')}}">Forgot password?</a>
                  <div class="text-end mt-3">
                    <button class="btn btn-primary btn-block w-100" type="submit">Sign in                 </button>
                  </div>
                </div>
                <div class="login-social-title">
                  <h3>Or Sign in with                 </h3>
                </div>
                <div class="form-group">
                  <ul class="login-social">
                    <li><a href="https://www.facebook.com" target="_blank"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="https://www.linkedin.com"><i class="fa fa-linkedin"> </i></a></li>
                    <li><a href="https://www.twitter.com" target="_blank"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="https://www.instagram.com" target="_blank"><i class="fa fa-instagram"></i></a></li>
                  </ul>
                </div>
                <p class="mt-4 mb-0 text-center">Don't have account?<a class="ms-2" href="{{ route('sign-up')}}">Create Account</a></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection

@extends('others.layout_others.master')

@section('others-content')
<div class="container-fluid p-0">
    <div class="comingsoon">
      <div class="comingsoon-inner text-center">
        <div class="logo-wrraper"><img class="img-fluid" src="{{ asset('assets/images/logo/logo.png') }}" alt="logo"></div>
        <h5>Let's Get Started With <br>KOHO</h5>
        <div class="svg-wrapper"><img class="img-fluid comingsoon-img" src="{{ asset('assets/images/other-images/comingsoon-img.svg') }}" alt="comingsoon-img"></div>
        <h6>We Are Coming Soon</h6>
        <div class="countdown" id="clockdiv">
          <ul>
            <li><span class="time" id="days"></span><span class="title">days</span></li>
            <li><span class="time" id="hours"></span><span class="title">Hours</span></li>
            <li><span class="time" id="minutes"></span><span class="title">Minutes</span></li>
            <li><span class="time" id="seconds"></span><span class="title">Seconds</span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('others-scripts')
<script src="{{ asset('assets/js/countdown.js') }}"></script>
@endsection

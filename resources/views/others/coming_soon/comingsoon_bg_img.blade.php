@extends('others.layout_others.master')

@section('others-content')
<div class="container-fluid p-0 m-0">
    <div class="comingsoon comingsoon-bgimg">
      <div class="comingsoon-inner text-center"><img src="{{ asset('assets/images/logo/logo.png') }}" alt="logo">
        <h5>WE ARE COMING SOON</h5>
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

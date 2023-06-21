@extends('others.layout_others.master')

@section('others-content')
    <!-- Maintenance start-->
    <div class="error-wrapper maintenance-bg">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="svg-wrapper"><img class="maintenance-img"
                            src="{{ asset('assets/images/other-images/maintenance-image.svg') }}" alt="maintenance-image"></div>
                </div>
                <div class="maintenance-heading">
                    <h2 class="headline">MAINTENANCE</h2>
                </div>
                <h4 class="sub-content">Our Site is Currently under maintenance We will be back Shortly<br> Thank You For
                    Patience</h4>
                <div><a class="btn btn-primary" href="{{ route('dashboard') }}">BACK TO HOME PAGE</a></div>
            </div>
        </div>
    </div>
    <!-- Maintenance end-->
@endsection

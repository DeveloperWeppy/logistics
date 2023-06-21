<!DOCTYPE html>
<html lang="en">

<head>
    <!-- All meta and title start-->
    @include('layout.head')
    <!-- meta and title end-->

    <!-- css start-->
    @include('layout.css')
    <!-- css end-->

</head>

<body>
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->

    <!-- Loader starts-->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>
    <!-- Loader ends-->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">

        <!-- Page Header Start-->
        @include('layout.header')
        <!-- Page Header Ends -->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">

        <!-- Page Sidebar Start-->
            @include('layout.sidebar')
        <!-- Page Sidebar Ends-->

        <div class="page-body">

            {{-- main body content --}}
            @yield('main-content')

        </div>
        <!-- footer start-->
            @include('layout.footer')
        <!-- footer end-->
        </div>
    </div>
    <!-- scripts start-->
    @include('layout.script')
    <!-- scripts end-->
</body>

</html>

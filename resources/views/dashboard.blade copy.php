@php
    function convertirValor($monto)
    {
        $valor = number_format($monto, 2, ',', '.');
        return $valor;
    }
@endphp
@extends('layout.master')

@section('title')
    Dashboard
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/chartist.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/slick-theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
@endsection

@section('main-content')
<div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>
                        Ecommerce Dashboard</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="home-item" href="{{ route('dashboard') }}"><i data-feather="home"></i></a>
                        </li>
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Ecommerce</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid ecommerce-page">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card sale-chart">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="sale-detail">
                                    <div class="icon"><i data-feather="shopping-bag"></i></div>
                                    <div class="sale-content">
                                        <h3>Total Sales</h3>
                                        <p>54,750 </p>
                                    </div>
                                </div>
                            </div>
                            <div class="small-chart-view sales-chart" id="sales-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card sale-chart">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="sale-detail">
                                    <div class="icon"><i data-feather="dollar-sign"></i></div>
                                    <div class="sale-content">
                                        <h3>Total Income</h3>
                                        <p>$35,532</p>
                                    </div>
                                </div>
                            </div>
                            <div class="small-chart-view income-chart" id="income-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card sale-chart">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="sale-detail">
                                    <div class="icon"><i data-feather="file-text"></i></div>
                                    <div class="sale-content">
                                        <h3>Orders Paid</h3>
                                        <p>55,900 </p>
                                    </div>
                                </div>
                            </div>
                            <div class="small-chart-view order-chart" id="order-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card sale-chart">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="sale-detail">
                                    <div class="icon"><i data-feather="users"></i></div>
                                    <div class="sale-content">
                                        <h3>Total Visitor</h3>
                                        <p>67,900</p>
                                    </div>
                                </div>
                            </div>
                            <div class="small-chart-view visitor-chart" id="visitor-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-5 col-xl-4 col-sm-6 box-col-40">
                <div class="card recent-order">
                    <div class="card-header pb-0">
                        <h3>Recent Orders</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li>
                                    <div><i class="icon-settings"></i></div>
                                </li>
                                <li><i class="view-html fa fa-code"></i></li>
                                <li><i class="icofont icofont-maximize full-card"></i></li>
                                <li><i class="icofont icofont-minus minimize-card"></i></li>
                                <li><i class="icofont icofont-refresh reload-card"></i></li>
                                <li><i class="icofont icofont-error close-card"> </i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body pb-0">
                        <div class="medium-chart">
                            <div class="recent-chart" id="recent-chart"></div>
                        </div>
                        <div class="code-box-copy">
                            <button class="code-box-copy__btn btn-clipboard" data-clipboard-target="#recentchart"
                                title="" data-bs-original-title="Copy" aria-label="Copy"><i
                                    class="icofont icofont-copy-alt"></i></button>
                            <pre><code class="language-html" id="recentchart">&lt;div class="card recent-order"&gt;
&lt;div class="card-header pb-0"&gt;
&lt;h3&gt;Recent Orders&lt;/h3&gt;
&lt;div class="card-header-right"&gt;
&lt;ul class="list-unstyled card-option"&gt;
 &lt;li&gt;
   &lt;div&gt;&lt;i class="icon-settings"&gt;&lt;/i&gt;&lt;/div&gt;
 &lt;/li&gt;
 &lt;li&gt;&lt;i class="view-html fa fa-code"&gt;&lt;/i&gt;&lt;/li&gt;
 &lt;li&gt;&lt;i class="icofont icofont-maximize full-card"&gt;&lt;/i&gt;&lt;/li&gt;
 &lt;li&gt;&lt;i class="icofont icofont-minus minimize-card"&gt;&lt;/i&gt;&lt;/li&gt;
 &lt;li&gt;&lt;i class="icofont icofont-refresh reload-card"&gt;&lt;/i&gt;&lt;/li&gt;
 &lt;li&gt;&lt;i class="icofont icofont-error close-card"&gt; &lt;/i&gt;&lt;/li&gt;
&lt;/ul&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="card-body pb-0"&gt;
&lt;div class="medium-chart"&gt;
&lt;div id="recent-chart"&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6 box-col-30">
                <div class="card top-products">
                    <div class="card-header pb-0">
                        <h3>Top Products</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li>
                                    <div><i class="icon-settings"></i></div>
                                </li>
                                <li><i class="view-html fa fa-code"></i></li>
                                <li><i class="icofont icofont-maximize full-card"></i></li>
                                <li><i class="icofont icofont-minus minimize-card"></i></li>
                                <li><i class="icofont icofont-refresh reload-card"></i></li>
                                <li><i class="icofont icofont-error close-card"> </i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordernone">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="icon"><img class="img-fluid"
                                                            src="{{ asset('assets/images/dashboard-2/chair.png') }}" alt="chair">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Wood Chair Dark</h5>
                                                    </a>
                                                    <p>100 Items</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Coupon Code</h5>
                                            <p>PIX001</p>
                                        </td>
                                        <td class="text-center"> <i class="flag-icon flag-icon-gb"></i></td>
                                        <td>
                                            <h5>-51%</h5>
                                            <p>$99.00 </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="icon"><img class="img-fluid"
                                                            src="{{ asset('assets/images/dashboard-2/shoes.png') }}" alt="chair">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Sneaker For Men</h5>
                                                    </a>
                                                    <p>150 Items</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Coupon Code</h5>
                                            <p>PIX002</p>
                                        </td>
                                        <td class="text-center"> <i class="flag-icon flag-icon-us"></i></td>
                                        <td>
                                            <h5>-78% </h5>
                                            <p>$66.00 </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="icon"><img class="img-fluid"
                                                            src="{{ asset('assets/images/dashboard-2/pot.png') }}" alt="pot">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Tree Stylish Pot </h5>
                                                    </a>
                                                    <p>105 Items </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Coupon Code</h5>
                                            <p>PIX003</p>
                                        </td>
                                        <td class="text-center"> <i class="flag-icon flag-icon-za"></i></td>
                                        <td>
                                            <h5>-04%</h5>
                                            <p>$116.00 </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="icon"><img class="img-fluid"
                                                            src="{{ asset('assets/images/dashboard-2/purse.png') }}" alt="purse">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Ulrich Duffel Bag</h5>
                                                    </a>
                                                    <p>600 Items </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Coupon Code</h5>
                                            <p>PIX004</p>
                                        </td>
                                        <td class="text-center"> <i class="flag-icon flag-icon-at"></i></td>
                                        <td>
                                            <h5>-60%</h5>
                                            <p>$99.00 </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="icon"><img class="img-fluid"
                                                            src="{{ asset('assets/images/dashboard-2/watch.png') }}" alt="watch">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Mi Watch Revolve</h5>
                                                    </a>
                                                    <p>541 Item </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>Coupon Code</h5>
                                            <p>PIX005</p>
                                        </td>
                                        <td class="text-center"> <i class="flag-icon flag-icon-br"></i></td>
                                        <td>
                                            <h5>-50%</h5>
                                            <p>$58.00 </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-box-copy">
                            <button class="code-box-copy__btn btn-clipboard" data-clipboard-target="#topproduct"
                                title="" data-bs-original-title="Copy" aria-label="Copy"><i
                                    class="icofont icofont-copy-alt"></i></button>
                            <pre><code class="language-html" id="topproduct">&lt;div class="card top-products"&gt;
&lt;div class="card-header pb-0"&gt;
&lt;h3&gt;Recent Orders&lt;/h3&gt;
&lt;div class="card-header-right"&gt;
&lt;ul class="list-unstyled card-option"&gt;
&lt;li&gt;&lt;div&gt;&lt;i class="icon-settings"&gt;&lt;/i&gt;&lt;/div&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="view-html fa fa-code"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-maximize full-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-minus minimize-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-refresh reload-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-error close-card"&gt; &lt;/i&gt;&lt;/li&gt;
&lt;/ul&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;div class="card-body pb-0"&gt;
&lt;div class="table-responsive"&gt;
&lt;table class="table table-bordernone"&gt;
&lt;tbody&gt;
 &lt;tr&gt;
   &lt;td&gt;&lt;div class="d-flex"&gt;&lt;div class="flex-shrink-0"&gt;
       &lt;div class="icon"&gt;
         &lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/chair.png') }}" alt="chair"/&gt;&lt;/div&gt;
       &lt;/div&gt;
       &lt;div class="flex-grow-1"&gt;
         &lt;h5&gt;Wood Chair Dark&lt;/h5&gt;
         &lt;p&gt;100 Items&lt;/p&gt;
       &lt;/div&gt;
     &lt;/div&gt;
   &lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;Coupon Code&lt;/h5&gt;&lt;p&gt;PIX001&lt;/p&gt;&lt;/td&gt;
   &lt;td class="text-center"&gt; &lt;i class="flag-icon flag-icon-gb"&gt;&lt;/i&gt;&lt;/td&gt;
   &lt;td&gt; &lt;h5&gt;-51%&lt;/h5&gt;&lt;p&gt;$99.00 &lt;/p&gt;&lt;/td&gt;
 &lt;/tr&gt;
 &lt;tr&gt;
   &lt;td&gt;
     &lt;div class="d-flex"&gt;
       &lt;div class="flex-shrink-0"&gt;
         &lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/shoes.png') }}" alt="chair"/&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="flex-grow-1"&gt;&lt;h5&gt;Sneaker For Men&lt;/h5&gt;&lt;p&gt;150 Items&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;
   &lt;/td&gt;
   &lt;td&gt; &lt;h5&gt;Coupon Code&lt;/h5&gt;&lt;p&gt;PIX002&lt;/p&gt;&lt;/td&gt;&lt;td class="text-center"&gt; &lt;i class="flag-icon flag-icon-us"&gt;&lt;/i&gt;&lt;/td&gt;&lt;td&gt;&lt;h5&gt;-78% &lt;/h5&gt;&lt;p&gt;$66.00 &lt;/p&gt;&lt;/td&gt;
 &lt;/tr&gt;
 &lt;tr&gt;
   &lt;td&gt; &lt;div class="d-flex"&gt;&lt;div class="flex-shrink-0"&gt;&lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/pot.png') }}" alt="pot"/&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="flex-grow-1"&gt;&lt;h5&gt;Tree Stylish Pot &lt;/h5&gt;&lt;p&gt;105 Items &lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
   &lt;td&gt; &lt;h5&gt;Coupon Code&lt;/h5&gt;&lt;p&gt;PIX003&lt;/p&gt;&lt;/td&gt;
   &lt;td class="text-center"&gt; &lt;i class="flag-icon flag-icon-za"&gt;&lt;/i&gt;&lt;/td&gt;
   &lt;td&gt;
     &lt;h5&gt;-04%&lt;/h5&gt;
     &lt;p&gt;$116.00 &lt;/p&gt;
   &lt;/td&gt;
 &lt;/tr&gt;
 &lt;tr&gt;
   &lt;td&gt;
     &lt;div class="d-flex"&gt;
       &lt;div class="flex-shrink-0"&gt;
         &lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/purse.png') }}" alt="purse"/&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="flex-grow-1"&gt;&lt;h5&gt;Ulrich Duffel Bag&lt;/h5&gt;&lt;p&gt;600 Items &lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
   lt;td&gt; &lt;h5&gt;Coupon Code&lt;/h5&gt;&lt;p&gt;PIX004&lt;/p&gt;&lt;/td&gt;
   &lt;td class="text-center"&gt; &lt;i class="flag-icon flag-icon-at"&gt;&lt;/i&gt;&lt;/td&gt;&lt;td&gt;&lt;h5&gt;-60%&lt;/h5&gt;&lt;p&gt;$99.00 &lt;/p&gt;&lt;/td&gt;
 &lt;/tr&gt;
 &lt;tr&gt;
   &lt;td&gt; &lt;div class="d-flex"&gt;&lt;div class="flex-shrink-0"&gt;&lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/watch.png') }}" alt="watch"/&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class="flex-grow-1"&gt;&lt;h5&gt;Mi Watch Revolve&lt;/h5&gt;&lt;p&gt;541 Item &lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
   &lt;td&gt; &lt;h5&gt;Coupon Code&lt;/h5&gt;&lt;p&gt;PIX005&lt;/p&gt;&lt;/td&gt;
   &lt;td class="text-center"&gt; &lt;i class="flag-icon flag-icon-br"&gt;&lt;/i&gt;&lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;-50%&lt;/h5&gt;&lt;p&gt;$58.00 &lt;/p&gt;&lt;/td&gt;
 &lt;/tr&gt;
&lt;/tbody&gt;
&lt;/table&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6 box-col-30">
                <div class="card country-sales-view">
                    <div class="card-header">
                        <h3>Sales By Countries</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li>
                                    <div><i class="icon-settings"></i></div>
                                </li>
                                <li><i class="view-html fa fa-code"></i></li>
                                <li><i class="icofont icofont-maximize full-card"></i></li>
                                <li><i class="icofont icofont-minus minimize-card"></i></li>
                                <li><i class="icofont icofont-refresh reload-card"></i></li>
                                <li><i class="icofont icofont-error close-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="medium-chart">
                            <div class="country-sales-chart" id="country-sales-chart"></div>
                        </div>
                        <div class="code-box-copy">
                            <button class="code-box-copy__btn btn-clipboard" data-clipboard-target="#contry-sale"
                                title="" data-bs-original-title="Copy" aria-label="Copy"><i
                                    class="icofont icofont-copy-alt"></i></button>
                            <pre><code class="language-html" id="contry-sale">&lt;div class="card country-sales-view"&gt;
&lt;div class="card-header"&gt;
&lt;h3&gt;Sales By Countries&lt;/h3&gt;
&lt;div class="card-header-right"&gt;
&lt;ul class="list-unstyled card-option"&gt;
&lt;li&gt;&lt;div&gt;&lt;i class="icon-settings"&gt;&lt;/i&gt;&lt;/div&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="view-html fa fa-code"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-maximize full-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-minus minimize-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-refresh reload-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-error close-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;/ul&gt;
&lt;/div&gt;
&lt;/div&gt;<
&lt;div class="card-body p-0"&gt;
&lt;div class="medium-chart"&gt;
&lt;div id="country-sales-chart"&gt;&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-5 col-xl-4 col-sm-6 box-col-30">
                <div class="card best-sellers">
                    <div class="card-header pb-0">
                        <h3> Best Sellers</h3>
                        <div class="card-header-right">
                            <ul class="list-unstyled card-option">
                                <li>
                                    <div><i class="icon-settings"></i></div>
                                </li>
                                <li><i class="view-html fa fa-code"></i></li>
                                <li><i class="icofont icofont-maximize full-card"></i></li>
                                <li><i class="icofont icofont-minus minimize-card"></i></li>
                                <li><i class="icofont icofont-refresh reload-card"></i></li>
                                <li><i class="icofont icofont-error close-card"></i></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordernone">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="icon"><img class="img-fluid"
                                                        src="{{ asset('assets/images/dashboard-2/person1.png') }}" alt="person1">
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>John Keter</h5>
                                                    </a>
                                                    <p>2019</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>06 August</h5>
                                        </td>
                                        <td>
                                            <h5>Brande Shoes</h5>
                                        </td>
                                        <td>
                                            <h5>$37,618 </h5>
                                        </td>
                                        <td>
                                            <div class="status-showcase">
                                                <p>65%</p>
                                                <div class="progress progress-bg-success" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 65%"
                                                        aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="icon"><img class="img-fluid"
                                                        src="{{ asset('assets/images/dashboard-2/person2.png') }}" alt="person2">
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Harry Venter</h5>
                                                    </a>
                                                    <p>2020</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>21 March</h5>
                                        </td>
                                        <td>
                                            <h5>Headphone</h5>
                                        </td>
                                        <td>
                                            <h5>$59,105 </h5>
                                        </td>
                                        <td>
                                            <div class="status-showcase">
                                                <p>45%</p>
                                                <div class="progress progress-bg-warning" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 45%"
                                                        aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="icon"><img class="img-fluid"
                                                        src="{{ asset('assets/images/dashboard-2/person3.png') }}" alt="person3">
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Loadin Deo</h5>
                                                    </a>
                                                    <p>2020</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>09 March</h5>
                                        </td>
                                        <td>
                                            <h5>Cell Phone</h5>
                                        </td>
                                        <td>
                                            <h5>$10,155 </h5>
                                        </td>
                                        <td>
                                            <div class="status-showcase">
                                                <p>85%</p>
                                                <div class="progress progress-bg-danger" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 85%"
                                                        aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="icon"><img class="img-fluid"
                                                        src="{{ asset('assets/images/dashboard-2/person4.png') }}" alt="person4">
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Horen Hors</h5>
                                                    </a>
                                                    <p>2020</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>14 February</h5>
                                        </td>
                                        <td>
                                            <h5>Fashion</h5>
                                        </td>
                                        <td>
                                            <h5>$90,568</h5>
                                        </td>
                                        <td>
                                            <div class="status-showcase">
                                                <p>75%</p>
                                                <div class="progress progress-bg-info" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 75%"
                                                        aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="icon"><img class="img-fluid"
                                                        src="{{ asset('assets/images/dashboard-2/person5.png') }}" alt="person5">
                                                </div>
                                                <div class="flex-grow-1"><a href="">
                                                        <h5>Fenter Jessy</h5>
                                                    </a>
                                                    <p>2020</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>21 January</h5>
                                        </td>
                                        <td>
                                            <h5>Bookshop</h5>
                                        </td>
                                        <td>
                                            <h5>$10,652</h5>
                                        </td>
                                        <td>
                                            <div class="status-showcase">
                                                <p>45%</p>
                                                <div class="progress progress-bg-warning" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 45%"
                                                        aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="code-box-copy">
                            <button class="code-box-copy__btn btn-clipboard" data-clipboard-target="#bestseller"
                                title="" data-bs-original-title="Copy" aria-label="Copy"><i
                                    class="icofont icofont-copy-alt"></i></button>
                            <pre><code class="language-html" id="bestseller">&lt;div class="card best-sellers"&gt;
&lt;div class="card-header pb-0"&gt;
&lt;h3&gt; Best Sellers&lt;/h3&gt;
&lt;div class="card-header-right"&gt;
&lt;ul class="list-unstyled card-option"&gt;
&lt;li&gt;&lt;div&gt;&lt;i class="icon-settings"&gt;&lt;/i&gt;&lt;/div&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="view-html fa fa-code"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-maximize full-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-minus minimize-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-refresh reload-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;li&gt;&lt;i class="icofont icofont-error close-card"&gt;&lt;/i&gt;&lt;/li&gt;
&lt;/ul&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;div class="card-body"&gt;
&lt;div class="table-responsive"&gt;
&lt;table class="table table-bordernone"&gt;
 &lt;thead&gt;
   &lt;tr&gt;
     &lt;th&gt;Name&lt;/th&gt;
     &lt;th&gt;Date&lt;/th&gt;&lt;th&gt;Product&lt;/th&gt;
     &lt;th&gt;Total&lt;/th&gt;
     &lt;th&gt;Status&lt;/th&gt;
   &lt;/tr&gt;
 &lt;/thead&gt;
 &lt;tbody&gt;
   &lt;tr&gt;
     &lt;td&gt;
       &lt;div class="d-flex"&gt;
         &lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/person1.png') }}" alt="person1"/&gt;&lt;/div&gt;
         &lt;div class="flex-grow-1"&gt;&lt;h5&gt;John Keter&lt;/h5&gt;
         &lt;p&gt;2019&lt;/p&gt;
         &lt;/div&gt;
       &lt;/div&gt;
     &lt;/td&gt;
     &lt;td&gt;&lt;h5&gt;06 August&lt;/h5&gt;&lt;/td&gt;
     &lt;td&gt;&lt;h5&gt;Brande Shoes&lt;/h5&gt;&lt;/td&gt;
     &lt;td&gt;&lt;h5&gt;$5,08,652 &lt;/h5&gt;&lt;/td&gt;
     &lt;td&gt;&lt;div class="status-showcase"&gt;&lt;p&gt;65%&lt;/p&gt;&lt;div class="progress progress-bg-success" style="height: 5px;"&gt;&lt;div class="progress-bar" role="progressbar" style="width: 65%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
 &lt;/tr&gt;
 &lt;tr&gt;
   &lt;td&gt;&lt;div class="d-flex"&gt;&lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/person2.png') }}" alt="person2"/&gt;&lt;/div&gt;&lt;div class="flex-grow-1"&gt;&lt;h5&gt;Harry Venter&lt;/h5&gt;&lt;p&gt;2020&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;21 March&lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;Headphone&lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;$59,105 &lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt;&lt;div class="status-showcase"&gt;&lt;p&gt;45%&lt;/p&gt;&lt;div class="progress progress-bg-warning" style="height: 5px;"&gt;&lt;div class="progress-bar" role="progressbar" style="width: 45%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
 &lt;/tr&gt;
 &lt;tr&gt;
   &lt;td&gt;
     &lt;div class="d-flex"&gt;
       &lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/person3.png') }}" alt="person3"/&gt;&lt;/div&gt;&lt;div class="flex-grow-1"&gt;&lt;h5&gt;Loadin Deo&lt;/h5&gt;&lt;p&gt;2020&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;09 March&lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;Cell Phone&lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;$10,155 &lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt;&lt;div class="status-showcase"&gt;&lt;p&gt;85%&lt;/p&gt;&lt;div class="progress progress-bg-danger" style="height: 5px;"&gt;&lt;div class="progress-bar" role="progressbar" style="width: 85%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
 &lt;/tr&gt;
 &lt;tr&gt;
   &lt;td&gt;&lt;div class="d-flex"&gt;&lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/person4.png') }}" alt="person4"/&gt;&lt;/div&gt;&lt;div class="flex-grow-1"&gt;&lt;h5&gt;Horen Hors&lt;/h5&gt;&lt;p&gt;2020&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
   &lt;td&gt;&lt;h5&gt;14 February&lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt; &lt;h5&gt;Fashion&lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt; &lt;h5&gt;$90,568&lt;/h5&gt;&lt;/td&gt;
   &lt;td&gt;&lt;div class="status-showcase"&gt;&lt;p&gt;75%&lt;/p&gt;&lt;div class="progress progress-bg-info" style="height: 5px;"&gt;&lt;div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
 &lt;/tr&gt;
 &lt;tr&gt;
   &lt;td&gt;
     &lt;div class="d-flex"&gt;&lt;div class="icon"&gt;&lt;img class="img-fluid" src="{{ asset('assets/images/dashboard-2/person5.png') }}" alt="person5"/&gt;&lt;/div&gt;&lt;div class="flex-grow-1"&gt;&lt;h5&gt;Fenter Jessy&lt;/h5&gt;&lt;p&gt;2020&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
     &lt;td&gt;&lt;h5&gt;21 January&lt;/h5&gt;&lt;/td&gt;
     &lt;td&gt; &lt;h5&gt;Bookshop&lt;/h5&gt;&lt;/td&gt;
     &lt;td&gt; &lt;h5&gt;$10,652&lt;/h5&gt;&lt;/td&gt;
     &lt;td&gt;&lt;div class="status-showcase"&gt;&lt;p&gt;45%&lt;/p&gt;&lt;div class="progress progress-bg-warning" style="height: 5px;"&gt;&lt;div class="progress-bar" role="progressbar" style="width: 45%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/td&gt;
 &lt;/tr&gt;
&lt;/tbody&gt;
&lt;/table&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-5 col-md-7 box-col-40">
                <div class="items-slider">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                        <div class="card rated-product bg-secondary">
                            <div class="card-body">
                                <div class="img-wrapper"><img class="img-fluid"
                                        src="{{ asset('assets/images/dashboard-2/wellington-shoes.png') }}"
                                        alt="wellington-shoes"><span
                                        class="badge badge-primary rated-product-badge">New</span></div>
                                <div class="product-detail"> <a href="">
                                        <h4>Wellington Shoes</h4>
                                    </a>
                                    <h3>$325.25</h3>
                                    <ul class="rating-star">
                                        <li> <i class="fa fa-star"></i></li>
                                        <li> <i class="fa fa-star"></i></li>
                                        <li> <i class="fa fa-star"></i></li>
                                        <li> <i class="fa fa-star"></i></li>
                                        <li> <i class="fa fa-star"></i></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                        <div class="card rated-product bg-primary">
                            <div class="card-body">
                                <div class="img-wrapper"><img class="img-fluid"
                                        src="{{ asset('assets/images/dashboard-2/apple-watch.png') }}" alt="apple-watch"><span
                                        class="badge badge-primary rated-product-badge">Hot</span></div>
                                <div class="product-detail"><a href="">
                                        <h4>Apple Smartwatch</h4>
                                    </a>
                                    <h3>$1185.99</h3>
                                    <ul class="rating-star">
                                        <li><i class="fa fa-star"></i></li>
                                        <li> <i class="fa fa-star"></i></li>
                                        <li> <i class="fa fa-star"></i></li>
                                        <li> <i class="fa fa-star"></i></li>
                                        <li> <i class="fa fa-star"></i></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                        <div class="card rated-product bg-secondary">
                            <div class="card-body">
                                <div class="img-wrapper"><img class="img-fluid"
                                        src="{{ asset('assets/images/dashboard-2/wellington-shoes.png') }}"
                                        alt="wellington-shoes"><span
                                        class="badge badge-primary rated-product-badge">new</span></div>
                                <div class="product-detail"><a href="">
                                        <h4>Wellington Shoes</h4>
                                    </a>
                                    <h3>$199.33</h3>
                                    <ul class="rating-star">
                                        <li><i class="fa fa-star"></i></li>
                                        <li><i class="fa fa-star"></i></li>
                                        <li><i class="fa fa-star"></i></li>
                                        <li><i class="fa fa-star"></i></li>
                                        <li><i class="fa fa-star"></i></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                        <div class="card rated-product bg-primary">
                            <div class="card-body">
                                <div class="img-wrapper"><img class="img-fluid"
                                        src="{{ asset('assets/images/dashboard-2/apple-watch.png') }}" alt="apple-watch"><span
                                        class="badge badge-primary rated-product-badge">Hot</span></div>
                                <div class="product-detail"><a href="">
                                        <h4>Apple Smartwatch</h4>
                                    </a>
                                    <h3>$990.50</h3>
                                    <ul class="rating-star">
                                        <li><i class="fa fa-star"></i></li>
                                        <li><i class="fa fa-star"></i></li>
                                        <li><i class="fa fa-star"></i></li>
                                        <li><i class="fa fa-star"></i></li>
                                        <li><i class="fa fa-star"></i></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card product-review">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="img-wrapper"><img class="img-fluid"
                                                src="{{ asset('assets/images/dashboard-2/person1.png') }}" alt="person1"></div>
                                    </div>
                                    <div class="person-detail"><a href="">
                                            <h4>Johanna Parvez</h4>
                                        </a>
                                        <ul class="rating-star">
                                            <li> <i class="fa fa-star"></i></li>
                                            <li> <i class="fa fa-star"></i></li>
                                            <li> <i class="fa fa-star"></i></li>
                                            <li> <i class="fa fa-star"></i></li>
                                            <li> <i class="fa fa-star"></i></li>
                                        </ul><i class="fa fa-quote-right fa-5x quote-icon"> </i>
                                    </div>
                                </div>
                                <div class="product-detail">
                                    <p>I love this good looking shoes, but comfort is where it’s at for Me. I can’t say how
                                        well they are for playing football , but for everyday wear they are amazing. I think
                                        they’re more comfortable than My lebron 17s.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-5 box-col-30">
                <div class="card weekend-view">
                    <div class="card-body">
                        <div class="inner-bg"></div>
                        <div class="img-wrapper"> <img class="img-fluid" src="{{ asset('assets/images/dashboard-2/headphone.png') }}"
                                alt="headphone"></div>
                        <div class="product-detail">
                            <h3>Special Weekend Offer</h3>
                            <h5>Upto 50% Off Discount</h5><a class="btn" href="">See More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart/chartist/chartist.js') }}"></script>
    <script src="{{ asset('assets/js/chart/chartist/chartist-plugin-tooltip.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
    <script src="{{ asset('assets/js/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
    <script src="{{ asset('assets/js/notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('assets/js/slick-slider/slick.min.js') }}"></script>
    <script src="{{ asset('assets/js/slick-slider/slick-theme.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead/handlebars.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead/typeahead.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead/typeahead.custom.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead-search/handlebars.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead-search/typeahead-custom.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/dashboard_2.js') }}"></script>
@endsection

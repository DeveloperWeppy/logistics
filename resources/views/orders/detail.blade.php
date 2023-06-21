@extends('layout.master')

@section('main-content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Invoice</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> <i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Ecommerce</li>
                        <li class="breadcrumb-item active">Invoice </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container invoice">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="row invo-profile">
                                <div class="col-xl-4">
                                    <div class="invo-profile-left">
                                        <div class="d-flex">
                                            <div class="d-flex-left"><img class="d-flex-object rounded-circle img-60"
                                            src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></div>
                                            <div class="flex-grow-1">
                                                <h4 class="d-flex-heading f-w-600"><?=$order->shipping->first_name?> <?=$order->shipping->last_name?></h4>
                                                <p><?=$order->billing->email?><br></span> <?=$order->shipping->city ?>,<?=$order->shipping->address_1?><span class="digits"><?=$order->shipping->phone?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-8">
                                        <div class="text-md-end text-xs-center">
                                            <h3>Pedido #<span class="digits counter"><?=$order->id?></span></h3>
                                            <p>Fecha: <span class="digits"><?=$order->date_created?></span></p>
                                        </div>
                                      
                                </div>
                            </div>
                            <div>
                                <div class="table-responsive invoice-table" id="table">
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                            <tr>
                                                <td class="item">
                                                    <h4 class="p-2 mb-0">Item Description</h4>
                                                </td>
                                                <td class="Hours">
                                                    <h4 class="p-2 mb-0">Hours</h4>
                                                </td>
                                                <td class="Rate">
                                                    <h4 class="p-2 mb-0">Rate</h4>
                                                </td>
                                                <td class="subtotal">
                                                    <h4 class="p-2 mb-0">Sub-total</h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Brown Dress</label>
                                                    <p class="m-0">Aask - Brown Polyester Blend Women's Fit & Flare Dress.</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">5</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">$75</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">$225</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Red Shirt</label>
                                                    <p class="m-0">Wild West - Red Cotton Blend Regular Fit Men's Formal Shirt.</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">3</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">$60</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">$180</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Flower Dress</label>
                                                    <p class="m-0">Skyblue Flower Printed Sleevless Strappy Dress.</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">10</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">$22</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">$220</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>Red Skirt</label>
                                                    <p class="m-0">R L F - Red Cotton Blend Women's A-Line Skirt.</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">10</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">$60</p>
                                                </td>
                                                <td>
                                                    <p class="itemtext digits">$600</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="itemtext"></p>
                                                </td>
                                                <td>
                                                    <p class="m-0">HST</p>
                                                </td>
                                                <td>
                                                    <p class="m-0 digits">13%</p>
                                                </td>
                                                <td>
                                                    <p class="m-0 digits">$419.25</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="Rate">
                                                    <h6 class="mb-0 p-2">Total</h6>
                                                </td>
                                                <td class="payment digits">
                                                    <h6 class="mb-0 p-2">$1,644.25</h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- End Table-->
                                <div class="row mt-3">
                                    <div class="col-md-8">
                                        <div>
                                            <p class="legal"><strong>Thank you for your business!</strong>  Payment is
                                                expected within 31 days; please process this invoice within that time. There
                                                will be a 5% interest charge per month on late invoices.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <form class="text-end invo-pal">
                                            <input type="image" src="{{ asset('assets/images/other-images/paypal.png') }}"
                                                name="submit" alt="PayPal - The safer, easier way to pay online!">
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End InvoiceBot-->
                        </div>
                        <div class="col-sm-12 text-center mt-3">
                            <button class="btn btn btn-primary me-2" type="button" onclick="myFunction()">Print</button>
                            <button class="btn btn-secondary" type="button">Cancel</button>
                        </div>
                        <!-- End Invoice-->
                        <!-- End Invoice Holder-->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
    <script src="{{ asset('assets/js/print.js') }}"></script>
@endsection

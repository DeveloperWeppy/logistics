@extends('layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
        .pricing-simple .card-body h1 {
            margin-bottom: 8px !important;
            font-size: 20px !important;
        }

        @media only screen and (max-width: 991px) {

            .page-wrapper .card .card-header,
            .page-wrapper .card .card-body,
            .page-wrapper .card .card-footer {
                padding: 12px !important;
            }
        }
    </style>
@endsection

@section('main-content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12">
                    <h3>Consultar Inventario</h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-pc">
                    <div class="card-header pb-0" style="display:flex;justify-content: space-between;">

                    </div>

                    <div class="card-body">
                        <div class="text-center">
                            <button class="btn btn-primary btn-create" type="button" data-bs-toggle="modal"
                                data-original-title="test" data-bs-target="#exampleModal" data-bs-original-title=""
                                title="">Consultar <i style="color:white;" class="mdi mdi-magnify"></i>
                            </button>
                        </div>

                        <div class="row">
                            <h3 id="name_product" class="text-center mt-2"></h3>
                        </div>
                        <div class="row" id="warehouses-container-original">
                            <hr>
                        </div>
                        <div class="row" id="warehouses-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('inventory.moda_consultar')
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/codeScanner/minified/html5-qrcode.min.js') }}"></script>
    <script>
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);


        function docReady(fn) {
            if (document.readyState === "complete" ||
                document.readyState === "interactive") {
                setTimeout(fn, 1);
            } else {
                document.addEventListener("DOMContentLoaded", fn);
            }
        }

        function abrirCamara() {
            docReady(function() {
                var resultContainer = document.getElementById('qr-reader-results');
                var lastResult, countResults = 0;

                function onScanSuccess(decodedText, decodedResult) {
                    if (decodedText !== lastResult) {
                        ++countResults;
                        lastResult = decodedText;
                        console.log(`Scan result ${decodedText}`, decodedResult);
                        var order_id = decodedText.split("/");
                        order_id = order_id[order_id.length - 1];
                        $("#cont-input-id").show();
                        $("#input-order-id").val(order_id);
                        html5QrcodeScanner.clear().then(_ => {}).catch(error => {

                        });
                    }
                }

                var html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", {
                        fps: 10,
                        qrbox: 250
                    });
                html5QrcodeScanner.render(onScanSuccess);
            });
        }
        $(document).ready(function() {
            $(document).on("click", ".btm-check", function() {  
                order_id=$(this).attr("data");
                $("#modalAprobar").modal('show');
            });
            $("#btn-scann-qr").on( "click", function() {
                $("#cont-input-id").hide();
                abrirCamara();
            } );
            $("#btn-create").on("click", function() {
                if ($("#input-order-id").val() != "") {
                    var orderId = $("#input-order-id").val();

                    // Limpiar el contenedor antes de agregar nuevas tarjetas
                    $('#warehouses-container').empty();
                    $('#warehouses-container-original').empty();

                    $.ajax({
                        url: "{{ route('inventory.search') }}/" + orderId,
                        method: "GET",
                        dataType: "json",
                        beforeSend: function() {
                            swal({
                                title: 'Consultando',
                                text: 'Espere un momento...',
                                button: false,
                                timer: 1500
                            });
                        },
                        success: function(response) {
                            // Maneja la respuesta del servidor aquí
                            if (response.data) {
                                //console.log(response.user_role);
                                if (response.data.results && response.data.results.length > 0) {
                                    // swal({
                                    //     icon: 'success',
                                    //     title: 'Información cargada correctamente',
                                    //     button: false,
                                    //     timer: 2500
                                    // });
                                    $('#name_product').text('Referencia: ' + response.data
                                        .results[0].name);

                                    // Dentro de la función success
                                    var warehouses = response.data.results[0].warehouses;
                                    var value_stock_original = 0;
                                    for (var i = 0; i < warehouses.length; i++) {
                                        var warehouse = warehouses[i];
                                        var quantity = warehouse.quantity;
                                        var name = warehouse.name;

                                        // Crear una nueva tarjeta
                                        // var cardHtml =
                                        //     '<div class="col-xl-3 col-sm-6 xl-25 box-col-3">';
                                        // cardHtml +=
                                        //     '<div class="card text-center pricing-simple">';
                                        // cardHtml += '<div class="card-body">';
                                        // cardHtml += '<h1> Stock:' + quantity + '</h1>';
                                        // cardHtml += '</div>';
                                        // cardHtml +=
                                        //     '<a class="btn btn-lg btn-secondary btn-block" href="javascript:void(0)">';
                                        // cardHtml += '<h5 class="mb-0">' + name + '</h5>';
                                        // cardHtml += '</a>';
                                        // cardHtml += '</div>';
                                        // cardHtml += '</div>';
                                        var cardHtml =
                                            '<div class="col-xl-3 col-sm-6 xl-25 box-col-3 mt-3">' +
                                            '<div class="row">' +
                                            '<div class="col-md-12">' +
                                            '<div class="btn-group btn-block d-flex">' +
                                            '<a class="btn btn-lg btn-info flex-fill" href="javascript:void(0)">' +
                                            '<h5 class="mb-0">' + name + '</h5>' +
                                            '</a>' +
                                            '<button class="btn btn-lg flex-fill" style="background-color: #534686; color: white;">' +
                                            '<h5 class="mb-0">' + quantity + '</h5>' +
                                            '</button>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>';


                                        if (name !== response.user_warehouse && name !== "Sin asignar" ) {
                                            // Agregar la tarjeta al contenedor deseado
                                            $('#warehouses-container').append(cardHtml);
                                        }
                                        if (name === response.user_warehouse) {
                                            value_stock_original = quantity;
                                        }
                                    }

                                    if (response.user_warehouse == "FERIAS") {
                                        var stock = response.product.stock;
                                        //console.log(response.product.stock);

                                        var cardHtmlOriginal =
                                            '<div class="col-md-6 offset-md-3">' +
                                            '<div class="card text-center pricing-simple">' +
                                            '<div class="card-body">';

                                            if (response.user_role !== "Inventario") {
                                            cardHtmlOriginal +=
                                                '<div class="input-group" id="stockInputGroup">' +
                                                '<span class="input-group-text">Restar del stock:</span>' +
                                                '<input type="number" class="form-control stock-input" value="0" id="stockInput" min="0">' +
                                                '<button class="btn btn-outline-secondary input-group-append update-stock-button" data-id="' +
                                                response.product.id + '" data-stock-actual="' +
                                                stock +
                                                '" type="button"><i class="fa fa-refresh"></i> Actualizar</button>' +
                                                '</div>';
                                            }

                                            cardHtmlOriginal +=
                                            '<div class="row mt-2">' +
                                            '<div class="col-md-12">' +
                                            '<div class="btn-group btn-block d-flex">' +
                                            '<a class="btn btn-lg btn-primary flex-fill" href="javascript:void(0)">' +
                                            '<h5 class="mb-0">' + response.user_warehouse + '</h5>' +
                                            '</a>' +
                                            '<button class="btn btn-lg flex-fill" style="background-color: #534686; color: white;">' +
                                            '<h5 class="mb-0">' + stock + '</h5>' +
                                            '</button>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>';


                                    } else {
                                        // var cardHtmlOriginal =
                                        //     '<div class="col-md-6 offset-md-3">';
                                        // cardHtmlOriginal +=
                                        //     '<div class="card text-center pricing-simple">';
                                        // cardHtmlOriginal += '<div class="card-body">';
                                        // cardHtmlOriginal += '<h1> Stock:' +
                                        //     value_stock_original + '</h1>';
                                        // cardHtmlOriginal += '</div>';
                                        // cardHtmlOriginal +=
                                        //     '<a class="btn btn-lg btn-info btn-block" href="javascript:void(0)">';
                                        // cardHtmlOriginal += '<h5 class="mb-0">' + response
                                        //     .user_warehouse + '</h5>';
                                        // cardHtmlOriginal += '</a>';
                                        // cardHtmlOriginal += '</div>';
                                        // cardHtmlOriginal += '</div>';
                                        var cardHtmlOriginal =
                                            '<div class="col-md-6 offset-md-3">' +
                                            '<div class="card text-center pricing-simple">' +
                                            '<div class="card-body">' +
                                            '<div class="row">' +
                                            '<div class="col-md-12">' +
                                            '<div class="btn-group btn-block d-flex">' +
                                            '<a class="btn btn-lg btn-primary flex-fill" href="javascript:void(0)">' +
                                            '<h5 class="mb-0">' + response.user_warehouse + '</h5>' +
                                            '</a>' +
                                            '<button class="btn btn-lg flex-fill" style="background-color: #534686; color: white;">' +
                                            '<h5 class="mb-0">' + value_stock_original + '</h5>' +
                                            '</button>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>';




                                    }

                                    $('#warehouses-container-original').append(cardHtmlOriginal);
                                    var stockInput = document.getElementById("stockInput");

                                    // Escucha el evento "change" para verificar el valor después de que se cambie
                                    stockInput.addEventListener("change", function() {
                                        var inputValue = parseInt(stockInput
                                        .value); // Convierte el valor a un número entero

                                        // Verifica si el valor es menor que 0
                                        if (inputValue < 0) {
                                            stockInput.value =
                                            0; // Establece el valor en 0 si es negativo
                                        }
                                    });
                                } else {
                                    console.error(
                                        'No se encontraron resultados en la respuesta.');
                                }


                            }


                        },
                        error: function(xhr, textStatus, errorThrown) {
                            // Maneja los errores de la solicitud aquí
                            //console.log(errorThrown);
                            swal({
                                icon: 'error',
                                title: 'Error',
                                text: 'Comunicate con el administrador',
                                button: false,
                                timer: 1500
                            });
                        }
                    });
                } else {
                    alert("Completa el campo");
                }
            });

            // Controlador de eventos delegado para botones de actualización
            $(document).on('click', '.update-stock-button', function() {
                var newStockValue = $(this).closest('.input-group').find('.stock-input').val();
                var productId = $(this).data('id');
                var stockActual = $(this).data('stock-actual');

                //console.log(newStockValue + productId + stockActual)
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: "{{ route('inventory.updateStock') }}",
                    method: "POST",
                    data: {
                        productId: productId,
                        newStockValue: newStockValue,
                        stockActual: stockActual
                    },
                    dataType: "json",
                    beforeSend: function() {
                        swal({
                            title: 'Actualizando Stock del Producto',
                            text: 'Espere un momento...',
                            button: false,
                            timer: 1500
                        });
                    },
                    success: function(response) {
                        // Maneja la respuesta del servidor aquí
                        if (response.success) {
                            var new_stock = response.newStock;
                            $('.stock_total').text(new_stock);

                            swal({
                                icon: 'success',
                                title: 'Actualizado',
                                text: response.message,
                                button: false,
                                timer: 1500
                            });
                        }else{
                            swal({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                button: false,
                                timer: 2500
                            });
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        // Maneja los errores de la solicitud aquí
                        console.error('Error en la petición AJAX ');
                        swal({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            button: false,
                            timer: 1500
                        });
                    }
                });
            });


        });
    </script>
@endsection

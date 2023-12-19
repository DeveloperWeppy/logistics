@extends('layout.master')

@section('title')
    Pedidos
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
@endsection

@section('main-content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-6">
                <h3>Pedidos</h3>
            </div>
            <div class="col-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> <i data-feather="home"></i></a></li>
                    <li class="breadcrumb-item">Pedidos Completados</li>
                  
                </ol>
            </div>
        </div>
    </div>
</div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-pc">
                    <div class="card-header pb-0" style="display:flex;justify-content:end;">
                        @if (Request::fullUrl() == Request::root().'/users')
                            <h3>Lista de Usuarios</h3>
                        @else
                            <h3></h3>
                        @endif
                        
                        {{-- @if (auth()->user()->getRoleNames()->first() != 'Despachador' && !isset($_GET['type']))
                        <button class="btn btn-primary btn-create mr-3" type="button" >Sincronizar Pedidos
                               <i style="color:white;" class="mdi mdi-sync"></i></button>
                        @endif --}}
                        {{-- <button type="button" class="btn btn-info ml-2" id="verifyOrderButton">Verificar Pedido
                            <i style="color:white;" class="mdi mdi-checkbox-marked-circle"></i>
                        </button> --}}

                        <input type="text" id="order_id_input" placeholder="Id Pedido QR" value="">
                    </div>
                    
                    <div class="card-body">
                        {{-- <div class="order-history table-responsive"> --}}
                            <table id="users-table" class="display responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Pedido</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Teléfono</th>
                                        <th scope="col">Método de Pago</th>
                                        <th scope="col">Ciudad</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Acción</th>
                                        <th scope="col">QR</th>
                                        <th scope="col">Factura Siigo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                            </table>
                           
                        {{-- </div> --}}
                    </div>
                </div>

                <div id="tarjeta-table">
                    {{-- @if (auth()->user()->getRoleNames()->first() != 'Despachador' && !isset($_GET['type']))
                        <button class="btn btn-primary btn-create" type="button">Sincronizar Pedidos   <i style="color:white;" class="mdi mdi-sync"></i></button>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    {{-- <script src="{{ asset('assets/js/codeScanner/minified/html5-qrcode.min.js') }}"></script> --}}
    <script> 
     var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    

        $(document).ready(function() {
            $('#order_id_input').focus();
            var order_id=0;
           
            // $("#btn-scann-qr").on( "click", function() {
            //     $("#cont-input-id").hide();
            //     abrirCamara();
            // } );
            $(".btn-create").on( "click", function() {
                swal({
                    title: "Sincronizar con la Web",
                    text: "¿Sincronizar facturas de la Web?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    })
                .then((willDelete) => {
                    if (willDelete) {
                        var url_ajax = "{{ route('orders.sync_invoices') }}";

                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                            },
                            type: "get",
                            encoding: "UTF-8",
                            url: url_ajax,
                            processData: false,
                            contentType: false,
                            dataType: 'json',

                            beforeSend: function() {
                                // Mostrar el mensaje de carga inicial
                                swal({
                                    title: "Cargando",
                                    text: "Procesando la información, espere un momento...",
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        swal.showLoading();
                                    },
                                    showConfirmButton: false,
                                    modal: true,
                                });
                            },

                            success: function(respuesta) {
                                swal.close();
                                //console.log(respuesta);

                                // Mostrar el mensaje de éxito después de actualizar el mensaje de SweetAlert
                                setTimeout(function() {
                                    swal({
                                        text: "Sincronización de Facturas completado!",
                                        type: 'success',
                                        showConfirmButton: false,
                                        timer: 2000
                                    })
                                }, 2000);
                                location.reload();
                            },

                            error: function(resp) {
                                //console.log(resp);
                                swal({
                                    title: "Se presentó un error!",
                                    text: 'Intenta otra vez, si persiste el error, comunícate con el área encargada, gracias.',
                                    icon: 'error',
                                });
                            }
                        });
                    } else {
                        swal("Facturas no sincronizadas!");
                    }
                });
            });
            if (isMobile) {
            
            } else {
                $("#tarjeta-table").hide();
                var table = $('#users-table').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.4/i18n/es_es.json"
                },
                processing: true,
                serverSide: true,
                // colReorder: true,
                pageLength: 10,
                //lengthChange: true, 
                lengthMenu: [10, 25, 50, 75, 100],
                ajax: '{{route('orders.get_orders_datatable')}}',
                
                order: [[0, 'DESC']],
                columns: [
                    { data: 'wc_order_id' },
                    { data: 'status_name' },
                    { data: 'customer' },
                    { data: 'phone' },
                    { data: 'payment_method' },
                    { data: 'city' },
                    { data: 'date' },
                    { data: 'total_amount' },
                    { data: 'edit' },
                    { data: 'qr' },
                    { data: 'siigo_invoice' },
                ]
                });
            }
            
            var timer;

            // Agrega un controlador de eventos para el evento 'input' en el input oculto
            // $(document).on('input', '#order_id_input', function () {
            $('#order_id_input').on('input', function () {
                // Si ya hay un temporizador en ejecución, límpialo
                if (timer) {
                    clearTimeout(timer);
                }

                // Configura un temporizador para verificar el valor del input después de 100 ms
                timer = setTimeout(function () {
                    // Obtiene el valor actual del input
                    var scannedOrderId = $('#order_id_input').val();
                    let skuEscaneado = scannedOrderId.trim();
                    console.log('valueee '+scannedOrderId);
                    handleScan(skuEscaneado);
                }, 100);
            });

        });
        function handleScan(scannedOrderId){
            if (scannedOrderId.trim() !== '') {
                    // Realiza una solicitud Ajax para verificar el ID del pedido
                    $.ajax({
                        url: '/orders/qr-validation/' + scannedOrderId,
                        method: 'GET',
                        beforeSend: function() {
                                // Mostrar el mensaje de carga inicial
                                swal({
                                    title: 'Validando',
                                    text: 'Por Favor espere',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    buttons: false,
                                    allowOutsideClick: false, 
                                });
                            },
                        success: function (data) {
                            if (data.valid) {
                                //console.log("respuesta ajax: " + data.valid);
                                if (data.order_status == 0) {
                                    if (data.payment_method === "Wompi") {
                                        if (data.responseStatusPayment === "APPROVED" && data.responseStatusPayment != null) {
                                            swal("Correcto!", "Pedido Correcto!", "success");
                                            window.location.href = '/orders/create/' + scannedOrderId;
                                        } else {
                                            if (data.payment_method === "Wompi") {
                                                $('#order_id_input').val('');
                                                swal("Información!", "El pago del pedido en WOMPI no ha sido aprovado correctamente!", "warning");
                                            } else {
                                                $('#order_id_input').val('');
                                                swal("Información!", "El pago del pedido en ADDI no ha sido aprovado correctamente!", "warning");
                                            }
                                        }
                                    }else{
                                        swal("Correcto!", "Pedido Correcto!", "success");
                                        window.location.href = '/orders/create/' + scannedOrderId;
                                    }
                                    
                                } else if(data.order_status == 1) {
                                    swal("Correcto!", "Pedido Correcto!", "success");
                                    window.location.href = '/orders/create/' + scannedOrderId;
                                    
                                }else if(data.order_status == 3){
                                    $('#order_id_input').val('');
                                    swal("Información!", "El pedido ya se encuentra completado!", "warning");
                                }
                                
                            } else {
                                $('#order_id_input').val('');
                                swal("Error!", "Número de pedido no válido!", "error");
                                $('#order_id_input').focus();
                            }
                        },
                        error: function () {
                            $('#order_id_input').val('');
                            swal("Error!", "Error al verificar el número de pedido!", "error");
                            $('#order_id_input').focus();
                        }
                    });
                } else {
                    $('#order_id_input').val('');
                    // Mostrar un mensaje de error si no hay valor escaneado
                    swal("Error!", "No hay número de pedido para verificar.", "error");
                    ('#order_id_input').focus();
                }
        }
        
    </script>
@endsection

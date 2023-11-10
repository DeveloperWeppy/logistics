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
                    <li class="breadcrumb-item">Pedidos</li>
                  
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
                        
                        @if (auth()->user()->getRoleNames()->first() != 'Despachador' && !isset($_GET['type']))
                        <button class="btn btn-primary btn-create mr-3" type="button" >Sincronizar Pedidos
                               <i style="color:white;" class="mdi mdi-sync"></i></button>
                        @endif
                        <button type="button" class="btn btn-info ml-2" id="verifyOrderButton">Verificar Pedido
                            <i style="color:white;" class="mdi mdi-checkbox-marked-circle"></i>
                        </button>

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
    {{-- <div class="modal fade " id="modalAprobar" tabindex="-1" aria-labelledby="modalAprobar" aria-modal="true" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Finalizar Pedido</h3>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <div class="modal-body row">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="col-md-12">
                                <label class="form-label" for="validationCustom01">Código de rastreo </label>
                                <input class="form-control" id="codrastreo" type="text" value="" required="">
                                <div class="valid-feedback"></div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-original-title="" title="">Cerrar</button>
                        <button class="btn btn-primary" id="btn-finalizar" type="button" data-bs-original-title="" title="">Finalizar</button>
                    </div>
                </div>
            </div>
    </div> --}}
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    {{-- <script src="{{ asset('assets/js/codeScanner/minified/html5-qrcode.min.js') }}"></script> --}}
    <script> 
     var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    

    function docReady(fn) {
        if (document.readyState === "complete"
            || document.readyState === "interactive") {
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }
    // function abrirCamara(){
    //     docReady(function () {
    //     var resultContainer = document.getElementById('qr-reader-results');
    //     var lastResult, countResults = 0;
    //     function onScanSuccess(decodedText, decodedResult) {
    //         if (decodedText !== lastResult) {
    //             ++countResults;
    //             lastResult = decodedText;
    //             console.log(`Scan result ${decodedText}`, decodedResult);
    //             var order_id=decodedText.split("/");
    //             order_id=order_id[order_id.length-1];
    //             $("#cont-input-id").show();
    //             $("#input-order-id").val(order_id);
    //             html5QrcodeScanner.clear().then(_ => {    
    //             }).catch(error => {
                   
    //             });
    //         }
    //     }

    //     var html5QrcodeScanner = new Html5QrcodeScanner(
    //         "qr-reader", { fps: 10, qrbox: 250 });
    //     html5QrcodeScanner.render(onScanSuccess);
    // });
    // }
        $(document).ready(function() {
            var order_id=0;
            $(document).on("click", ".btm-check", function() {  
                order_id=$(this).attr("data");
                $("#modalAprobar").modal('show');
            });
            $(document).on("click", "#btn-finalizar", function() {  
                var csrfToken = document.querySelector('input[name="_token"]').value;
                fetch("{{ route('orders.store');}}/"+order_id+"/"+1, {
                method: 'POST',
                body: JSON.stringify({cod:$("#codrastreo").val()}), 
                headers:{
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                    
                }
                }).then(res => res.json())
                .catch(error => console.error('Error:', error))
                .then(response =>  {
                    swal({
                    icon: 'success',
                    title: 'Guardado',
                    showConfirmButton: false,
                    timer: 1500
                    });
                    location.href ="{{ route('orders');}}";
                });
            });
           
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
            //    $(".card-pc").hide();
            //    fetch("{{route('orders.get_orders_datatable')}}", {
            //     method: 'get',
            //     headers: {
            //         'Content-Type': 'application/json'
            //     }
            //     })
            //     .then(res => res.json())
            //     .catch(error => console.error('Error:', error))
            //     .then(response => {
            //         var list="";
            //         var cont=$("#tarjeta-table").html();
            //         for (let i = 0; i < response.data.length; i++) {
            //             const billing =JSON.parse(response.data[i].billing);
            //             const fecha = new Date(response.data[i].created_at);
            //             let fechat= fecha.getDate()+"/"+(fecha.getMonth()+1)+"/"+fecha.getFullYear()+" "+fecha.getHours()+":"+fecha.getMinutes();
            //             list+= `
            //             <div class="col-md-12 mb-3">
            //                 <div class="card">
            //                     <div class="card-body">
            //                     <div class="row">
            //                         <div class="col-10">
            //                         #: <span class="email">${response.data[i].wc_order_id}</span><br>
            //                         Fecha: <span class="email">${fechat}</span><br>
            //                         Cliente: <span class="email">${response.data[i].customer}</span><br>
            //                         Creador: <span class="email">${response.data[i].customer}</span><br>
            //                         Estado: <span class="email">${response.data[i].status_name}</span>
            //                         </div>
            //                         <div class="col-2" style="display: flex; justify-content: center; align-items: center;">
            //                           ${response.data[i].edit}
            //                         </div>
            //                     </div>
            //                     </div>
            //                 </div>
            //             </div>
            //         `;
            //         }

            //         $("#tarjeta-table").html(cont+"<div style='width:100%'>"+list+"</div>");
            //     });

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
                    
                ]
                });
            }
            // Supongamos que tienes un campo de entrada oculto con el ID "order_id_input"
            // let orderInput = document.getElementById('order_id_input');

            // // Escucha el evento de escaneo del QR
            // tuElementoQRScanner.addEventListener('scan', function (event) {
            //     //let scannedOrderId = event.detail; // Suponiendo que el evento devuelve el ID del pedido escaneado
            //     let qrUrl = event.detail;
            //     let orderId = qrUrl.split("/").pop();

            //     // Realiza una solicitud Ajax para verificar el ID del pedido
            //     $.ajax({
            //         url: '/orders/qr-validation/' + orderId,
            //         method: 'GET',
            //         success: function (data) {
            //             // Manejar la respuesta del servidor
            //             if (data.valid) {
            //                 // Si el pedido es válido, redirige al detalle del pedido
            //                 window.location.href = '/orders/create/' + orderId;
            //             } else {
            //                 // Si el pedido no es válido, muestra un mensaje de error o maneja según sea necesario
            //                 //alert('Número de pedido no válido');
            //                 swal("Error!", "Número de pedido no válido!", "error");
            //             }
            //         },
            //         error: function () {
            //             // Manejar errores de la solicitud Ajax
            //             //alert('Error al verificar el número de pedido');
            //             swal("Error!", "Error al verificar el número de pedido!", "error");
            //         }
            //     });
            // });
            // // Agrega un escuchador de eventos para la entrada de teclado
            // document.addEventListener('keydown', function (event) {
            //     console.log('keydown: '+document.activeElement.id);
            //     // Verifica si el campo de entrada oculto está enfocado
            //     if (document.activeElement.id === 'order_id_input') {
            //         console.log("event.key: "+event.key);
            //         // Llama a la función de manejo del escaneo
            //         handleScan({ data: event.key });
            //     }
            // });
            // document.addEventListener('keypress', function (event) {
            //     if (event.key === 'Enter') {
            //         event.preventDefault();
            //     }
            // });
           // document.addEventListener('keydown', function (event) {
                //let scannedOrderId = event.target.value;
                // Llama a la función de manejo del escaneo
                // if (scannedOrderId.length === 6) {
                    // Llama a la función de manejo del escaneo
                    // if (document.activeElement.id === 'order_id_input') {
                        // Forza la asignación del valor escaneado al campo de entrada
                        //document.getElementById('order_id_input').value += event.key;
                    // }
                // }
                //handleScan({ data: event.target.value });
            //});
            // $(document).on('keydown', function (event) {
            //     console.log('keydown: ' + $(document.activeElement).attr('id'));
            //     // Verifica si el campo de entrada oculto está enfocado
            //     if ($(document.activeElement).attr('id') === 'order_id_input') {
            //         // Forza la asignación del valor escaneado al campo de entrada
            //         $('#order_id_input').val(function (index, value) {
            //             return value + event.key;
            //         });

            //         // Llama a la función de manejo del escaneo
            //         //handleScan();
            //     }
            // });
            // document.addEventListener('DOMContentLoaded', function() {
            //     // Pone el foco en el input oculto al cargar la página
            //     document.getElementById('order_id_input').focus();
            // });
            // $(document).on('input', '#order_id_input', function (event) {
            //     console.log("eveto: "+event);
            //     // Verifica si el campo de entrada oculto está enfocado
            //     if ($(document.activeElement).attr('id') === 'order_id_input') {
            //         // Forza la asignación del valor escaneado al campo de entrada
            //         $('#order_id_input').val(function (index, value) {
            //             return value + event.data;
            //         });

            //         // Llama a la función de manejo del escaneo
            //         //handleScan();
            //     }
            // });
            var timer;

            // Agrega un controlador de eventos para el evento 'input' en el input oculto
            $(document).on('input', '#order_id_input', function () {
                // Si ya hay un temporizador en ejecución, límpialo
                if (timer) {
                    clearTimeout(timer);
                }

                // Configura un temporizador para verificar el valor del input después de 100 ms
                timer = setTimeout(function () {
                    // Obtiene el valor actual del input
                    var scannedOrderId = $('#order_id_input').val();
                    console.log('valueee '+scannedOrderId);
                    // Verifica si el campo de entrada oculto está enfocado
                    if ($(document.activeElement).attr('id') === 'order_id_input') {
                        // Llama a la función de manejo del escaneo con el valor escaneado
                        //handleScan(scannedOrderId);
                    }
                }, 100);
            });

            // Simulación de escaneo con un valor arbitrario "123456"
            //$('#order_id_input').val('123456').trigger('input');

            document.getElementById('verifyOrderButton').addEventListener('click', function () {
                // Obtén el valor del input oculto
                let scannedOrderId = $('#order_id_input').val();

                console.log("orderId: " + scannedOrderId);
                // Verifica si hay algún valor
                if (scannedOrderId.trim() !== '') {
                    // Realiza una solicitud Ajax para verificar el ID del pedido
                    $.ajax({
                        url: '/orders/qr-validation/' + scannedOrderId,
                        method: 'GET',
                        success: function (data) {
                            if (data.valid) {
                                console.log("respuesta ajax: " + data.valid);
                                swal("Correcto!", "Pedido Correcto!", "success");
                            } else {
                                swal("Error!", "Número de pedido no válido!", "error");
                            }
                        },
                        error: function () {
                            swal("Error!", "Error al verificar el número de pedido!", "error");
                        }
                    });
                } else {
                    // Mostrar un mensaje de error si no hay valor escaneado
                    swal("Error!", "No hay número de pedido para verificar.", "error");
                }
            });


        });
        // Función para manejar el escaneo
        // function handleScan(scannedOrderId) {
        //     // Extrae el número del pedido de la URL
        //     // Extrae el número del pedido de la URL
        //     // let orderId = scannedOrderId.split("logistic.weppydev.com.co").pop();
            
        //     // // Limpia el número del pedido de caracteres no deseados (por ejemplo, "?q=")
        //     // orderId = orderId.replace(/[^\d]/g, '');

        //     // document.getElementById('order_id_input').value = orderId;
        //     //document.getElementById('order_id_input').value = scannedOrderId;
        //     console.log('order_id_input: '+scannedOrderId);
        //     //Realiza una solicitud Ajax para verificar el ID del pedido
        //     $.ajax({
        //         url: '/orders/qr-validation/' + scannedOrderId,
        //         method: 'GET',
        //         success: function (data) {
        //             if (data.valid) {
        //                 //window.location.href = '/orders/create/' + orderId;
        //                 console.log("respuesta ajax:" +data.valid)
        //                 swal("Correcto!", "Pedido Correcto!", "success");
        //             } else {
        //                 swal("Error!", "Número de pedido no válido!", "error");
        //             }
        //         },
        //         error: function () {
        //             swal("Error!", "Error al verificar el número de pedido!", "error");
        //         }
        //     });
        // }
        
    </script>
@endsection

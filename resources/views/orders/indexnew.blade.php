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
                    <div class="card-header pb-0" style="display:flex;justify-content: space-between;">
                        @if (Request::fullUrl() == Request::root().'/users')
                            <h3>Lista de Usuarios</h3>
                        @else
                            <h3></h3>
                        @endif
                        
                        @if (auth()->user()->getRoleNames()->first() != 'Despachador' && !isset($_GET['type']))
                        <button class="btn btn-primary btn-create" type="button" >Sincronizar Pedidos
                               <i style="color:white;" class="mdi mdi-sync"></i></button>
                        @endif
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
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                </tbody>
                            </table>
                           
                        {{-- </div> --}}
                    </div>
                </div>

                <div id="tarjeta-table">
                    @if (auth()->user()->getRoleNames()->first() != 'Despachador' && !isset($_GET['type']))
                        <button class="btn btn-primary btn-create" type="button">Sincronizar Pedidos   <i style="color:white;" class="mdi mdi-sync"></i></button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade " id="modalAprobar" tabindex="-1" aria-labelledby="modalAprobar" aria-modal="true" role="dialog">
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
    </div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets/js/codeScanner/minified/html5-qrcode.min.js') }}"></script>
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
    function abrirCamara(){
        docReady(function () {
        var resultContainer = document.getElementById('qr-reader-results');
        var lastResult, countResults = 0;
        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText !== lastResult) {
                ++countResults;
                lastResult = decodedText;
                console.log(`Scan result ${decodedText}`, decodedResult);
                var order_id=decodedText.split("/");
                order_id=order_id[order_id.length-1];
                $("#cont-input-id").show();
                $("#input-order-id").val(order_id);
                html5QrcodeScanner.clear().then(_ => {    
                }).catch(error => {
                   
                });
            }
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    });
    }
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
           
            $("#btn-scann-qr").on( "click", function() {
                $("#cont-input-id").hide();
                abrirCamara();
            } );
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
                                    allowOutsideClick: false,
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
               $(".card-pc").hide();
               fetch("{{route('orders.get_orders_datatable')}}", {
                method: 'get',
                headers: {
                    'Content-Type': 'application/json'
                }
                })
                .then(res => res.json())
                .catch(error => console.error('Error:', error))
                .then(response => {
                    var list="";
                    var cont=$("#tarjeta-table").html();
                    for (let i = 0; i < response.data.length; i++) {
                        const billing =JSON.parse(response.data[i].billing);
                        const fecha = new Date(response.data[i].created_at);
                        let fechat= fecha.getDate()+"/"+(fecha.getMonth()+1)+"/"+fecha.getFullYear()+" "+fecha.getHours()+":"+fecha.getMinutes();
                        list+= `
                        <div class="col-md-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                <div class="row">
                                    <div class="col-10">
                                    #: <span class="email">${response.data[i].wc_order_id}</span><br>
                                    Fecha: <span class="email">${fechat}</span><br>
                                    Cliente: <span class="email">${response.data[i].customer}</span><br>
                                    Creador: <span class="email">${response.data[i].customer}</span><br>
                                    Estado: <span class="email">${response.data[i].status_name}</span>
                                    </div>
                                    <div class="col-2" style="display: flex; justify-content: center; align-items: center;">
                                      ${response.data[i].edit}
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    `;
                    }

                    $("#tarjeta-table").html(cont+"<div style='width:100%'>"+list+"</div>");
                });

            } else {
                $("#tarjeta-table").hide();
                var table = $('#users-table').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.4/i18n/es_es.json"
                },
                processing: true,
                serverSide: true,
                colReorder: true,
                pageLength: 10,
                lengthChange: true, 
                lengthMenu: [10, 25, 50, 75, 100],
                ajax: '{{route('orders.get_orders_datatable')}}',
                
                order: [[0, 'DESC']],
                columns: [
                    { data: 'wc_order_id', name: 'wc_order_id' },
                    { data: 'status_name', name: 'status_name' }
                    { data: 'customer', name: 'customer' },
                    { data: 'phone', name: 'phone' },
                    { data: 'payment_method', name: 'payment_method' },
                    { data: 'city', name: 'city' },
                    { data: 'date', name: 'date' },
                    { data: 'total', name: 'total' },
                    //{ data: 'name_user', name: 'name_user' },
                    { data: 'edit', name: 'edit' }
                    
                ]
                });
            }
        });
    </script>
@endsection

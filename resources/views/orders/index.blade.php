@extends('layout.master')

@section('title')
    Pedidos
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
@endsection

@section('main-content')
    @include('common.crumbs', ['title' => 'Pedidos','crumbs'=>['Pedidos']])
    @include('common.table', ['title' => $type == "" ? 'Activos' : 'Completados', 'titles' => ['#', 'Cliente', 'Creador', 'Estado', 'Acción']])
    <div class="modal fade " id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Agregar Pedido</h3>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <div class="modal-body row">
                        <div class="input-group pill-input-group leftright-radius" id="cont-input-id"><span  id="btn-scann-qr" class="input-group-text"><i class="icofont icofont-qr-code"> </i></span>
                                            <input id="input-order-id" class="form-control" type="number" ><span class="input-group-text"><i class="icofont icofont-stock-search">
                                                </i></span>
                        </div>
                        <div id="qr-reader" style="width:500px"></div>
                        <div id="qr-reader-results"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="btn-create" type="button" data-bs-dismiss="modal" data-bs-original-title="" title="">Crear</button>
                        <button class="btn btn-secondary" type="button" data-bs-original-title="" title="">Cerrar</button>
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
                                <input class="form-control" id="validationCustom01" type="text" value="" required="">
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
                body: JSON.stringify({}), 
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
            $("#btn-create").on( "click", function() {
                if($("#input-order-id").val()!=""){
                    location.href='{!! route('orders.create') !!}/'+$("#input-order-id").val();
                }else{
                    alert("Completa el campo");
                }
            } );
            if (isMobile) {
               $(".card-pc").hide();
               fetch("{{ route('orders.get') }}", {
                method: 'get',
                headers: {
                    'Content-Type': 'application/json'
                }
                })
                .then(res => res.json())
                .catch(error => console.error('Error:', error))
                .then(response => {
                    alert(response.data[0].billing);
                    var list="";
                    for (let i = 0; i < response.data.length; i++) {
                        const billing =JSON.parse(response.data[i].billing);
                        list+= `
                        <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-body">
                            <div class="row">
                                <div class="col-10">
                                <h5 class="card-title">Cliente: <span class="name">${billing.first_name} ${billing.last_name}</span></h5>
                                <p class="card-text">Creador: <span class="email">${response.data[i].customer}</span></p>
                                <p class="card-text">Estado: <span class="phone">${response.data[i].status_name}</span></p>
                                <!-- Agrega más campos según tus necesidades -->
                                </div>
                                <a class="col-2" href="{{url('orders/create')}}/${response.data[i].wc_order_id}" style="display: flex;justify-content: center;align-items: center">
                                <i class="mdi mdi-checkbox-blank-outline"></i>
                                </a>
                            </div>
                            </div>
                        </div>
                        </div>
                    `;
                    }
                    $("#tarjeta-table").html(list);
                });

            } else {
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
                ajax: '{{route('orders.get')."/".$type}}',
                
                order: [[0, 'DESC']],
                columns: [
                    { data: 'wc_order_id', name: 'wc_order_id' },
                    { data: 'customer', name: 'customer' },
                    { data: 'name_user', name: 'name_user' },
                    { data: 'status_name', name: 'status_name' },
                    { data: 'edit', name: 'edit' }
                    
                ]
                });
            }
        });
    </script>
@endsection

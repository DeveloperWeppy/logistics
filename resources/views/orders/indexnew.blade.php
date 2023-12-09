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
    <!-- Agregar el token CSRF solo en esta vista -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                        
                        @if(@Auth::user()->hasRole('Admin') || @Auth::user()->hasRole('Picking'))
                        <button class="btn btn-info mr-3 ms-2" type="button" id="generate-qr-selected">Crear QR de Seleccionados
                            <i style="color:white;" class="mdi mdi-qrcode"></i></button>
                        <button class="btn btn-primary btn-create mr-3" type="button" >Sincronizar Pedidos
                               <i style="color:white;" class="mdi mdi-sync"></i></button>
                        @endif
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
                                        <th scope="col"></th>
                                        <th scope="col">Pedido</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Teléfono</th>
                                        <th scope="col">Método de Pago</th>
                                        <th scope="col">Ciudad</th>
                                        <th scope="col">Fecha de Pago</th>
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
                pageLength: 30,
                //lengthChange: true, 
                lengthMenu: [30, 60, 90, 120, 200],
                ajax: '{{route('orders.get_orders_datatable')}}',
                searching: true,
                order: [[0, 'DESC']],
                columns: [
                    { data: null, orderable: false, render: function (data, type, row) {
                        return '<input type="checkbox" class="check-row" data-order-id="' + row.wc_order_id + '">';
                    }},
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

            // Manejar la selección/deselección de todos los checkboxes
            $('#check-all').change(function () {
                $('.check-row').prop('checked', this.checked);
            });

            // Manejar la generación masiva de QR al hacer clic en el botón correspondiente
            $('#generate-qr-selected').click(function () {
                var selectedOrders = [];

                // Obtener los IDs de los pedidos seleccionados
                $('.check-row:checked').each(function () {
                    selectedOrders.push($(this).data('order-id'));
                });

                // Validar que hay al menos un pedido seleccionado
                if (selectedOrders.length === 0) {
                    // Mostrar una alerta con SweetAlert
                    swal({
                        text: "Por favor, selecciona al menos un pedido para generar QR",
                        icon: 'error',
                        showConfirmButton: true,
                    });
                    return; // Detener la ejecución si no hay pedidos seleccionados
                }

                // Realizar la solicitud AJAX para generar los QR de los pedidos seleccionados
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route("orders.generate_qr_selected") }}',
                    type: 'POST',
                    data: { orders: selectedOrders },
                    success: function (response) {
                        console.log(response);
                        if (response.success) {
                            swal({
                                text: "Se ha generado los QR de los pedidos seleccionados",
                                icon: 'success',
                                showConfirmButton: true,
                            });
                            openPdfTab(response.html);
                        }else{
                            swal({
                                text: "Ha ocurrido un error al intentar generar los QR de los pedidos seleccionados",
                                icon: 'error',
                                showConfirmButton: true,
                            });
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
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

        // Función para abrir una nueva pestaña y enviar los datos HTML
        function openPdfTab(htmlData) {
            // Obtener el token CSRF
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Crear un formulario temporal
            var form = document.createElement('form');
            form.method = 'post';
            form.action = '{{ route("orders.pdf_qr_masivos") }}';
            form.target = '_blank';

            // Agregar un campo de entrada para el token CSRF
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Agregar un campo de entrada para los datos HTML
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'htmlData';
            input.value = JSON.stringify(htmlData);
            form.appendChild(input);

            // Agregar el formulario al documento y enviarlo
            document.body.appendChild(form);
            form.submit();

            // Eliminar el formulario temporal
            document.body.removeChild(form);
        }
    </script>
@endsection

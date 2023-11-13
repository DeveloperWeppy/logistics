{{-- @extends('layout.master')

@section('main-content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Pedidos </h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> <i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Pedidos</li>
                        <li class="breadcrumb-item active">Agregar </li>
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
                            <div>

                            </div>
                            <!-- End InvoiceTop -->
                            <div class="row invo-profile">
                                <div class="col-xl-4">
                                    <div class="invo-profile-left">
                                        <div class="d-flex">
                                            <div class="d-flex-left"><img class="d-flex-object rounded-circle img-60"
                                            src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></div>
                                            <div class="flex-grow-1">
                                                <h4 class="d-flex-heading f-w-600"><?=$data['billing']['first_name']?> <?=$data['billing']['last_name']?></h4>
                                                <p><?=$data['billing']['email']?><br></span> <?=$data['shipping']['city']?>,<?=$data['shipping']['address_1']?><span class="digits"><?=$data['billing']['phone']?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-8">
                                        <div class="text-md-end text-xs-center">
                                            <h3>Pedido #<span class="digits counter"><?=$data['id']?></span></h3>
                                            <p>Fecha: <span class="digits"><?=$data['date_created']?></span></p>
                                        </div>
                                </div>
                                <div class="col-xl-12">
                                        <div style="width:100%;align-items:center;display:flex;justify-content: space-between;">
                                           <span><span class="font-weight-bold">Estado:</span> {{$status_name}} </span>
                                           <button onclick="abrirCamara()"  style="float: right;" class="btn btn-primary" type="button" data-bs-toggle="modal" data-original-title="test" data-bs-target="#exampleModal" data-bs-original-title="" title="">   <i style="color:white;" class="mdi mdi-qrcode"></i></button>
                                        </div>
                                        <div>
                                            <div class="progress-bar2">
                                                <div class="step active2"><i class="mdi mdi-format-list-checks"></i></div>
                                                <div class="line2 active2"></div>
                                                <div class="step {{ isset($picking) ? 'active2' : '' }}"><i class="mdi mdi-package-variant"></i></div>
                                                <div class="line2  {{ isset($picking) ? 'active2' : '' }}"></div>
                                                <div class="step {{ isset($packing) ? 'active2' : '' }}"><i class="mdi mdi-package-variant-closed"></i></div>
                                                <div class="line2  {{ isset($packing) ? 'active2' : '' }}"></div>
                                                <div class="step"><i class="mdi mdi-approval"></i></div>
                                            </div>
                                            <div class="progress-content active2" id="content-1">
                                                <h3>Creador:{{$creador->value('name')}} {{$creador->value('last_name')}}</h3>
                                                <span>Fecha:{{$order->created_at}}</span>
                                            </div>
                                            @if ($status == 1)
                                                <div class="progress-content " id="content-2">
                                                    <h3>Picking:{{$picking->value('name')}} {{$picking->value('last_name')}}</h3>
                                                    <span>Fecha:{{$order->date_picking}}</span>
                                                </div> 
                                            @endif
                                            @if ($status == 2)
                                                <div class="progress-content " id="content-3">
                                                    <h3>Packing:{{$packing->value('name')}} {{$delivery->value('last_name')}}</h3>
                                                    <span>Fecha:{{$order->date_delivery}}</span>
                                                </div> 
                                            @endif
                                        </div>
                                </div>
                            </div>
                            <div>
                                <div class="table-responsive invoice-table card-pc" id="table">
                                    <table class="table table-bordered table-striped">
                                        <tr>    
                                                <td class="item">
                                                    <h4 class="p-2 mb-0">Imagen</h4>
                                                </td>
                                                <td class="item">
                                                    <h4 class="p-2 mb-0">Sku</h4>
                                                </td>
                                                <td class="item">
                                                    <h4 class="p-2 mb-0">Nombre</h4>
                                                </td>
                                                <td class="Hours">
                                                    <h4 class="p-2 mb-0">Cantidad</h4>
                                                </td>
                                                <td class="Rate">
                                                    <h4 class="p-2 mb-0">Cantidad Scann</h4>
                                                </td>
                                                <td class="subtotal">
                                                    <h4 class="p-2 mb-0">Validado</h4>
                                                </td>
                                         </tr>
                                        <tbody id="t-products">
                                            
                                            <?php for($i = 0; $i < count($data['line_items']); $i++) { ?>
                                            <tr>
                                                <td class="sorting_1"><img src="<?= $data_items[$i]['image'] ?>" alt="" style="width: 80px;height:auto"></td>
                                                <td ><?= $data_items[$i]['sku'] ?></td>
                                                <td><label><?= $data['line_items'][$i]['name'] ?> </label></td>
                                                <td><p class="itemtext digits"><?= $data['line_items'][$i]['quantity'] ?></p></td>
                                                <td><p class="itemtext digits">0</p></td>
                                                <td></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="width:93%;margin-left:7%;margin-top:15px">
                                    <div id="tarjeta-table">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 text-center mt-3">
                            <button id="btn-finalizar" class="btn btn btn-primary me-2" type="button" style="display:none">{{$status==1?"Enviar a Delivery":"Enviar a Packing"}} </button>
                            <a class="btn btn-secondary" href="{{route('orders')}}"  type="button">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade show" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Agregar Producto</h3>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <div class="modal-body row">
                        <div id="qr-reader" style="width:500px"></div>
                        <div id="qr-reader-results"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-original-title="" title="">Cerrar</button>
                    </div>
                </div>
            </div>
    </div>
    <style>
    .progress-bar2 {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 80%;
      margin: 20px auto;
    }
    
    .step {
      position: relative;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      text-align: center;
      line-height: 30px;
      font-size: 14px;
      font-weight: bold;
      background-color: #ccc;
      color: #fff;
    }
    
    .step.active2 {
      background-color: #007bff;
    }
    
    .progress-bar2::before {
      content: "";
      position: absolute;
      top: 14px;
      left: 50%;
      right: 50%;
      height: 2px;
      background-color: #ccc;
      z-index: 1;
    }
    
    .progress-bar2 .line2 {
      flex-grow: 1;
      height: 2px;
      background-color: #ccc;
      z-index: 1;
    }
    
    .progress-bar2 .line2.active2 {
      background-color: #007bff;
    }
    .progress-content {
      display: none;
      margin-top: 20px;
      padding: 10px;
      background-color: #f2f2f2;
      border-radius: 5px;
    }
    
    .progress-content.active2 {
      display: block;
    }
    .font-weight-bold{
        font-weight: bold;
    }
  </style>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/codeScanner/minified/html5-qrcode.min.js') }}"></script>
    <script>
            window.addEventListener('DOMContentLoaded', function() {
      var steps = document.querySelectorAll('.step');
      var progressContents = document.querySelectorAll('.progress-content');

      steps.forEach(function(step, index) {
        step.addEventListener('click', function() {
          // Ocultar todos los contenidos
          progressContents.forEach(function(content) {
            content.classList.remove('active2');
          });

          // Mostrar el contenido correspondiente al paso seleccionado
          var contentId = 'content-' + (index + 1);
          var selectedContent = document.getElementById(contentId);
          if (selectedContent) {
            selectedContent.classList.add('active2');
          }
        });
      });
    });
     var audioScanner = new Audio('{{asset('assets/audio/scanner.mp3') }}');
     var audioError= new Audio('{{asset('assets/audio/error.mp3') }}');
     var arrayData = @json($data_items);
     var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
     if (isMobile) {
        let list="";
        let cont="";
        $(".card-pc").hide();
        for (var i = 0; i < arrayData.length; i++) {
            icon="";
            if(arrayData[i].quantity==arrayData[i].scann){
                cont++;
                icon='<i class="mdi mdi-check" style="color:#28a745"></i>';
            }
            list+= `
                        <div class="col-md-12 mb-3">
                            <div class="card">
                                <div class="card-body" style="padding: 0;">
                                <div class="row">
                                    <div class="col-4" style="background-image: url('${arrayData[i].image}'); background-size: contain;background-repeat: no-repeat;">
                                        
                                    </div>
                                    <div class="col-8 row" style="display: flex; justify-content: center; align-items: center;padding-top:10px;">
                                         <span class="name col-12 font-weight-bold" >${arrayData[i].name}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Sku:</span> ${arrayData[i].sku}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Cantidad</span>: ${arrayData[i].quantity}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Scann</span>:${arrayData[i].scann}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Validado</span>: ${icon}</span>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
             
                        `;
        }
        $("#tarjeta-table").html(list);
     }

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
        var loader=false;
        function onScanSuccess(decodedText, decodedResult) {
            if(loader){
              return;
            }
            loader=true;
            ++countResults;
            lastResult = decodedText;
            console.log(`Scan result ${decodedText}`, decodedResult);
            var order_id = decodedText.split("/").pop().trim();
            var rowIndex = arrayData.findIndex(function(row) {
                return row.sku === order_id;
            });
            if (rowIndex >= 0) {
                if(arrayData[rowIndex].quantity==arrayData[rowIndex].scann){
                    audioError.play();
                    mensaje("info","No puedes agregar","Por que supera la cantidad del pedido");
                }else{
                    mensaje("success","Agregado","El producto fue agregado");
                    audioScanner.play();
                    modificarTab(rowIndex, html5QrcodeScanner);
                }
            } else {
                mensaje("error","Error","El producto no esta en el pedido");
                audioError.play();
            }
            setTimeout(() => {loader=false}, 3000);

        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
        $("#exampleModal").on('hidden.bs.modal', function () {
            html5QrcodeScanner.clear().then(_ => {    
                }).catch(error => {
                   
             });
        });
    });
    }
    function modificarTab(index,html5QrcodeScanner) {
        arrayData[index].scann = arrayData[index].scann + 1;
        var tabla = "";
        var cont=0;
        var icon="";
        for (var i = 0; i < arrayData.length; i++) {
            icon="";
            if(arrayData[i].quantity==arrayData[i].scann){
                cont++;
                icon='<i class="mdi mdi-check" style="color:#28a745"></i>';
            }
            if (isMobile) {
                tabla+= `
                        <div class="col-md-12 mb-3">
                            <div class="card">
                                <div class="card-body" style="padding: 0;">
                                <div class="row">
                                    <div class="col-4" style="background-image: url('${arrayData[i].image}'); background-size: contain;background-repeat: no-repeat;">
                                        
                                    </div>
                                    <div class="col-8 row" style="display: flex; justify-content: center; align-items: center;padding-top:10px;">
                                         <span class="name col-12 font-weight-bold">${arrayData[i].name}</span>
                                         <span class="name col-12"><span class="font-weight-bold">sku:</span> ${arrayData[i].sku}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Cantidad:</span>: ${arrayData[i].quantity}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Scann:</span>${arrayData[i].scann}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Validado:</span>${icon}</span>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
             
                        `;
            }else{
                tabla += `
            <tr>
                <td class="sorting_1"><img src="${arrayData[i].image}" alt="" style="width: 80px;height:auto"></td>
                <td><label> ${arrayData[i].sku}</label></td>
                <td><label>${arrayData[i].name}</label></td>
                <td><p class="itemtext digits">${arrayData[i].quantity}</p></td>
                <td><p class="itemtext digits">${arrayData[i].scann}</p></td>
                <td>${icon}</td>
            </tr>
            `;
            }

        }
        if(cont==arrayData.length){
            html5QrcodeScanner.clear().then(_ => {    
                }).catch(error => {
                   
             });
             $("#exampleModal").modal('hide');
             $("#btn-finalizar").show();
             
        }
        if (isMobile) {
            $("#tarjeta-table").html(tabla);
        }else{
            document.getElementById("t-products").innerHTML = tabla;
        }
     
       
    }
    $("#btn-finalizar").on( "click", function() {
        swal({
            title: 'Guardando ',
            text: 'Por Favor espere',
            timer: 2000,
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false,
            allowOutsideClick: false, 
        });
        fetch("{{ route('orders.store',['id'=>$id,'type'=>0]);}}", {
        method: 'POST',
        body: JSON.stringify({}), 
        headers:{
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
        }).then(res => res.json())
        .catch(error => console.error('Error:', error))
        .then(response =>  {
            //alert(JSON.stringify(response));
            swal({
            icon: 'success',
            title: 'Guardado',
            showConfirmButton: false,
            timer: 1500
            });
            location.href ="{{ route('orders');}}";
         });
    } );
    function mensaje(icon,title,text){
        swal({
            icon: icon,
            title:title,
            text: text,
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false
            });

    }
    </script>
    <script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
    <script src="{{ asset('assets/js/print.js') }}"></script>
@endsection --}}

@extends('layout.master')

@section('main-content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Pedidos </h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> <i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Pedidos</li>
                        <li class="breadcrumb-item active">Agregar </li>
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
                            <div>

                            </div>
                            <!-- End InvoiceTop -->
                            <div class="row invo-profile">
                                <div class="col-xl-4">
                                    <div class="invo-profile-left">
                                        <div class="d-flex">
                                            <div class="d-flex-left"><img class="d-flex-object rounded-circle img-60"
                                            src="{{ asset('assets/images/logo/logo-icon.png') }}" alt=""></div>
                                            <div class="flex-grow-1">
                                                <h4 class="d-flex-heading f-w-600"><?=$data['billing']['first_name']?> <?=$data['billing']['last_name']?></h4>
                                                <p><?=$data['billing']['email']?><br></span> <?=$data['shipping']['city']?>,<?=$data['shipping']['address_1']?><span class="digits"><?=$data['billing']['phone']?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-8">
                                        <div class="text-md-end text-xs-center">
                                            <h3>Pedido #<span class="digits counter"><?=$data['id']?></span></h3>
                                            <p>Fecha: <span class="digits"><?=$data['date_created']?></span></p>
                                        </div>
                                </div>
                                <div class="col-xl-12">
                                        <div style="width:100%;align-items:center;display:flex;justify-content: space-between;">
                                           <span><span class="font-weight-bold">Estado:</span> {{$status_name}} </span>
                                            <span style="display: flex; align-items: center;">
                                                <span class="font-weight-bold">Producto Escaneado:</span>
                                                <input type="text" id="product_id_input" placeholder="Id Producto QR" class="form-control" value="">
                                            </span>
                                        </div>
                                        <div>
                                            <div class="progress-bar2">
                                                <div class="step active2"><i class="mdi mdi-format-list-checks"></i></div>
                                                <div class="line2 active2"></div>
                                                <div class="step {{ isset($picking) ? 'active2' : '' }}"><i class="mdi mdi-package-variant"></i></div>
                                                <div class="line2  {{ isset($picking) ? 'active2' : '' }}"></div>
                                                <div class="step {{ isset($packing) ? 'active2' : '' }}"><i class="mdi mdi-package-variant-closed"></i></div>
                                                <div class="line2  {{ isset($packing) ? 'active2' : '' }}"></div>
                                                <div class="step"><i class="mdi mdi-approval"></i></div>
                                            </div>
                                            <div class="progress-content active2" id="content-1">
                                                <h3>Creador:{{$creador->value('name')}} {{$creador->value('last_name')}}</h3>
                                                <span>Fecha:{{$order->created_at}}</span>
                                            </div>
                                            @if ($status == 1)
                                                <div class="progress-content " id="content-2">
                                                    <h3>Picking:{{$picking->value('name')}} {{$picking->value('last_name')}}</h3>
                                                    <span>Fecha:{{$order->date_picking}}</span>
                                                </div> 
                                            @endif
                                            @if ($status == 2)
                                                <div class="progress-content " id="content-3">
                                                    <h3>Packing:{{$packing->value('name')}} {{$delivery->value('last_name')}}</h3>
                                                    <span>Fecha:{{$order->date_delivery}}</span>
                                                </div> 
                                            @endif
                                        </div>
                                </div>
                            </div>
                            <div>
                                <div class="table-responsive invoice-table card-pc" id="table">
                                    <table class="table table-bordered table-striped">
                                        <tr>    
                                                <td class="item">
                                                    <h4 class="p-2 mb-0">Imagen</h4>
                                                </td>
                                                <td class="item">
                                                    <h4 class="p-2 mb-0">Sku</h4>
                                                </td>
                                                <td class="item">
                                                    <h4 class="p-2 mb-0">Nombre</h4>
                                                </td>
                                                <td class="Hours">
                                                    <h4 class="p-2 mb-0">Cantidad</h4>
                                                </td>
                                                <td class="Rate">
                                                    <h4 class="p-2 mb-0">Cantidad Scann</h4>
                                                </td>
                                                <td class="subtotal">
                                                    <h4 class="p-2 mb-0">Validado</h4>
                                                </td>
                                         </tr>
                                        <tbody id="t-products">
                                            
                                            <?php for($i = 0; $i < count($data['line_items']); $i++) { ?>
                                            <tr>
                                                <td class="sorting_1"><img src="<?= $data_items[$i]['image'] ?>" alt="" style="width: 80px;height:auto"></td>
                                                <td ><?= $data_items[$i]['sku'] ?></td>
                                                <td><label><?= $data['line_items'][$i]['name'] ?> </label></td>
                                                <td><p class="itemtext digits"><?= $data['line_items'][$i]['quantity'] ?></p></td>
                                                <td><p class="itemtext digits">0</p></td>
                                                <td></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="width:93%;margin-left:7%;margin-top:15px">
                                    <div id="tarjeta-table">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 text-center mt-3">
                            <button id="btn-finalizar" class="btn btn btn-primary me-2" type="button" style="display:none">{{$status==1?"Enviar a Delivery":"Enviar a Packing"}} </button>
                            <button id="btn-domicilio-cali" class="btn btn btn-primary me-2" type="button" style="display:none">Domicilio Cali </button>
                            <a class="btn btn-secondary" href="{{route('orders')}}"  type="button">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade show" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Agregar Producto</h3>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <div class="modal-body row">
                        <div id="qr-reader" style="width:500px"></div>
                        <div id="qr-reader-results"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-original-title="" title="">Cerrar</button>
                    </div>
                </div>
            </div>
    </div>
    <style>
    .progress-bar2 {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 80%;
      margin: 20px auto;
    }
    
    .step {
      position: relative;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      text-align: center;
      line-height: 30px;
      font-size: 14px;
      font-weight: bold;
      background-color: #ccc;
      color: #fff;
    }
    
    .step.active2 {
      background-color: #007bff;
    }
    
    .progress-bar2::before {
      content: "";
      position: absolute;
      top: 14px;
      left: 50%;
      right: 50%;
      height: 2px;
      background-color: #ccc;
      z-index: 1;
    }
    
    .progress-bar2 .line2 {
      flex-grow: 1;
      height: 2px;
      background-color: #ccc;
      z-index: 1;
    }
    
    .progress-bar2 .line2.active2 {
      background-color: #007bff;
    }
    .progress-content {
      display: none;
      margin-top: 20px;
      padding: 10px;
      background-color: #f2f2f2;
      border-radius: 5px;
    }
    
    .progress-content.active2 {
      display: block;
    }
    .font-weight-bold{
        font-weight: bold;
    }
  </style>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/codeScanner/minified/html5-qrcode.min.js') }}"></script>
    <script>
    window.addEventListener('DOMContentLoaded', function() {
      var steps = document.querySelectorAll('.step');
      var progressContents = document.querySelectorAll('.progress-content');

      steps.forEach(function(step, index) {
        step.addEventListener('click', function() {
          // Ocultar todos los contenidos
          progressContents.forEach(function(content) {
            content.classList.remove('active2');
          });

          // Mostrar el contenido correspondiente al paso seleccionado
          var contentId = 'content-' + (index + 1);
          var selectedContent = document.getElementById(contentId);
          if (selectedContent) {
            selectedContent.classList.add('active2');
          }
        });
      });
    });
     var audioScanner = new Audio('{{asset('assets/audio/scanner.mp3') }}');
     var audioError= new Audio('{{asset('assets/audio/error.mp3') }}');
     var arrayData = @json($data_items);
     var dataOrder = @json($data);
     var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
     if (isMobile) {
        let list="";
        let cont="";
        $(".card-pc").hide();
        for (var i = 0; i < arrayData.length; i++) {
            icon="";
            if(arrayData[i].quantity==arrayData[i].scann){
                cont++;
                icon='<i class="mdi mdi-check" style="color:#28a745"></i>';
            }
            list+= `
                        <div class="col-md-12 mb-3">
                            <div class="card">
                                <div class="card-body" style="padding: 0;">
                                <div class="row">
                                    <div class="col-4" style="background-image: url('${arrayData[i].image}'); background-size: contain;background-repeat: no-repeat;">
                                        
                                    </div>
                                    <div class="col-8 row" style="display: flex; justify-content: center; align-items: center;padding-top:10px;">
                                         <span class="name col-12 font-weight-bold" >${arrayData[i].name}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Sku:</span> ${arrayData[i].sku}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Cantidad</span>: ${arrayData[i].quantity}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Scann</span>:${arrayData[i].scann}</span>
                                         <span class="name col-12"><span class="font-weight-bold">Validado</span>: ${icon}</span>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
             
                        `;
        }
        $("#tarjeta-table").html(list);
     }

    $("#btn-finalizar").on( "click", function() {
        swal({
            title: 'Guardando ',
            text: 'Por Favor espere',
            timer: 2000,
            showConfirmButton: false,
            showCancelButton: false,
            buttons: false,
            allowOutsideClick: false, 
        });
        fetch("{{ route('orders.store',['id'=>$id,'type'=>0]);}}", {
        method: 'POST',
        body: JSON.stringify({}), 
        headers:{
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
        }).then(res => res.json())
        .catch(error => console.error('Error:', error))
        .then(response =>  {
            //alert(JSON.stringify(response));
            swal({
            icon: 'success',
            title: 'Guardado',
            showConfirmButton: false,
            timer: 1500
            });
            location.href ="{{ route('orders');}}";
         });
    } );
    function mensaje(icon,title,text){
        swal({
            icon: icon,
            title:title,
            text: text,
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false
            });

    }
    $(document).ready(function() {
        var cityValue = dataOrder.billing.city;
        console.log("items: "+cityValue);

        $('#product_id_input').focus();
        //console.log("cantidad items "+arrayData.length);
        // Escucha el evento de entrada (input) del producto
        $('#product_id_input').on('input', function () {
            let codigoEscaneado = $('#product_id_input').val();
            // Vaciar el valor
            $('#product_id_input').val('');
            let skuEscaneado = codigoEscaneado.trim();
            console.log("escaneado:"+skuEscaneado);
           
            var rowIndex = arrayData.findIndex(function(row) {
                return row.sku === skuEscaneado;
            });

            if (rowIndex >= 0) {
                if (arrayData[rowIndex].quantity === arrayData[rowIndex].scann) {
                    audioError.play();
                    mensaje("info", "No puedes agregar", "Por que supera la cantidad del pedido");
                } else {
                    mensaje("success", "Agregado", "El producto fue agregado");
                    audioScanner.play();
                    modificarTab(rowIndex);
                }
            } else {
                mensaje("error", "Error", "El producto no está en el pedido");
                audioError.play();
            }
            
            $('#product_id_input').focus();
        });
    });        

        function modificarTab(index) {
            arrayData[index].scann = arrayData[index].scann + 1;
            var tabla = "";
            var cont=0;
            var icon="";
            for (var i = 0; i < arrayData.length; i++) {
                icon="";
                if(arrayData[i].quantity==arrayData[i].scann){
                    cont++;
                    icon='<i class="mdi mdi-check" style="color:#28a745"></i>';
                }
                tabla += `
                    <tr>
                        <td class="sorting_1"><img src="${arrayData[i].image}" alt="" style="width: 80px;height:auto"></td>
                        <td><label> ${arrayData[i].sku}</label></td>
                        <td><label>${arrayData[i].name}</label></td>
                        <td><p class="itemtext digits">${arrayData[i].quantity}</p></td>
                        <td><p class="itemtext digits">${arrayData[i].scann}</p></td>
                        <td>${icon}</td>
                    </tr>
                    `;
                    document.getElementById("t-products").innerHTML = tabla;
            }
            if(cont==arrayData.length){
                habilitarBotonAccion();
            }
        }

        // Función para habilitar el botón de acción adicional
        function habilitarBotonAccion() {
            // Habilita el botón
            $("#btn-finalizar").show();
        }
    </script>
    <script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
    <script src="{{ asset('assets/js/print.js') }}"></script>
@endsection 
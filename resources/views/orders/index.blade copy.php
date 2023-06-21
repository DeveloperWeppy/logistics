@extends('layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main-content')
    @include('common.crumbs', ['title' => 'Pedidos','crumbs'=>['Pedidos']])
    @include('common.table', ['title' => 'En Proceso','titles'=>['#','Cliente','Credor','Estado','Accion']])
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/codeScanner/minified/html5-qrcode.min.js') }}"></script>
    <script> 
function docReady(fn) {
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // Llamar en la siguiente ejecución disponible
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

docReady(function () {
    var resultContainer = document.getElementById('qr-reader-results');
    var lastResult, countResults = 0;
    
    function onScanSuccess(decodedText, decodedResult) {
        if (decodedText !== lastResult) {
            ++countResults;
            lastResult = decodedText;
            // Manejar la condición de éxito con el mensaje decodificado.
            console.log(`Resultado de escaneo: ${decodedText}`, decodedResult);

            // Obtener la URL del código QR decodificado
            var qrUrl = extractUrlFromDecodedText(decodedText);
            
            // Mostrar una alerta con la URL del código QR
            alert("URL del código QR: " + qrUrl);
        }
    }

    var html5QrCodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
    html5QrCodeScanner.render(onScanSuccess);
});

function extractUrlFromDecodedText(decodedText) {
    // Implementa la lógica para extraer la URL del texto decodificado del código QR
    // y devuelve la URL extraída.
    // Puedes utilizar expresiones regulares u otros métodos de manipulación de cadenas.
    // Por ejemplo:
    // var url = decodedText.match(/https?:\/\/\S+/);
    // return url ? url[0] : null;

    // En este ejemplo, se asume que el texto decodificado es la URL directamente.
    return decodedText;
}
        $(document).ready(function() {
            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                colReorder: true,
                pageLength: 10,
                lengthChange: true, 
                lengthMenu: [10, 25, 50, 75, 100],
                ajax: '{!! route('orders.get') !!}',
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },
                order: [[0, 'DESC']],
                columns: [
                    { data: 'wc_order_id', name: 'wc_order_id' },
                    { data: 'customer', name: 'customer' },
                    { data: 'create_user_id', name: 'create_user_id' },
                    { data: 'status', name: 'status' },
                    { data: 'edit', name: 'edit' }
                    
                ]
            });
        });
    </script>
@endsection

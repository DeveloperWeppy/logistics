@extends('layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main-content')
    @include('common.crumbs', ['title' => 'Usuarios','crumbs'=>['usuarios']])
    @include('common.table', ['title' => 'Lista de Usuarios','titles'=>['Nombre','Documento','Email','Direccion','Rol']])
    <!-- Container-fluid starts-->

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script> 
$(document).ready(function() {
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        colReorder: true,
        pageLength: 10, 
        lengthChange: true, // Permitir al usuario seleccionar cuántos elementos ver por página
        lengthMenu: [10, 25, 50, 75, 100],
        ajax: '{!! route('users.get') !!}',
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        order: [[0, 'DESC']],
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' }
        ]
    });
});
</script>
@endsection

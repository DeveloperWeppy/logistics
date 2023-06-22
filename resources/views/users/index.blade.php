@extends('layout.master')

@section('title')
    Usuarios
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
@endsection

@section('main-content')
    @include('common.crumbs', ['title' => 'Usuarios','crumbs'=>['usuarios']])
    @include('common.table', ['title' => 'Lista de Usuarios','titles'=>['#','Nombre','Email','Rol','Accion']])
    <!-- Container-fluid starts-->

@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
        <script> 
        $(document).ready(function() {
            $(".btn-create").click(function(){
                location.href='{{route('users.create')}}';
            });
            var table = $('#users-table').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.4/i18n/es_es.json"
                },
                processing: true,
                serverSide: true,
                colReorder: true,
                pageLength: 10, // Mostrar 10 elementos por página
                lengthChange: true, // Permitir al usuario seleccionar cuántos elementos ver por página
                lengthMenu: [10, 25, 50, 75, 100],
                ajax: '{!! route('users.get') !!}',
                order: [[0, 'DESC']],
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'roles[0].name', name: 'rol_name' },
                    { data: 'edit', name: 'edit' }
                ]
            });
        });
    </script>
@endsection
